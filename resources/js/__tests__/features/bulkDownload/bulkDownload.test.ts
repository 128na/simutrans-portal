import { describe, expect, it, vi } from "vitest";
import {
  DEFAULT_RETRY_AFTER_SECONDS,
  downloadSequentially,
  fileNameFromDisposition,
  isLongWait,
  LONG_WAIT_SECONDS,
  parseRetryAfter,
  type BulkItem,
  type BulkProgress,
} from "@/features/bulkDownload/bulkDownload";

const postA: BulkItem = {
  id: 11,
  title: "通勤電車",
  url: "/users/a/train",
  download_url: "/articles/11/download",
};
const postB: BulkItem = {
  id: 22,
  title: "高架駅",
  url: "/users/b/station",
  // APP_URL と開いているホストが違う場合を想定した別ホストの絶対 URL
  download_url: "https://other-host.example/articles/22/download",
};
const intro: BulkItem = {
  id: 33,
  title: "外部の橋",
  url: "/users/c/bridge",
  addon_page_url: "/articles/33/conversion",
};

const ok = (fileName: string) =>
  new Response(new Blob([fileName]), {
    status: 200,
    headers: {
      "Content-Disposition": `attachment; filename=fallback.zip; filename*=utf-8''${encodeURIComponent(fileName)}`,
    },
  });

const tooMany = (retryAfter: string) =>
  new Response("", { status: 429, headers: { "Retry-After": retryAfter } });

const setup = (responses: Record<string, Array<Response | Error>>) => {
  const calls: Array<{ url: string; origin: string; init?: RequestInit }> = [];
  const fetchImpl = vi.fn(
    async (input: RequestInfo | URL, init?: RequestInit) => {
      const parsed = new URL(String(input));
      const url = parsed.pathname;
      calls.push({ url, origin: parsed.origin, init });
      const next = responses[url]?.shift();
      if (!next) {
        throw new Error(`unexpected fetch ${url}`);
      }
      if (next instanceof Error) {
        throw next;
      }
      return next;
    }
  ) as unknown as typeof fetch;
  const saveBlob = vi.fn();
  // 眠った分だけ時計を進める
  const clock = { ms: Date.parse("2026-11-11T05:00:00Z") };
  const sleep = vi.fn(async (ms: number) => {
    clock.ms += ms;
  });
  const now = () => clock.ms;
  const progress: BulkProgress[] = [];
  return {
    calls,
    fetchImpl,
    saveBlob,
    sleep,
    clock,
    progress,
    options: {
      fetchImpl,
      saveBlob,
      sleep,
      now,
      onProgress: (p: BulkProgress) => progress.push(p),
    },
  };
};

describe("downloadSequentially", () => {
  it("アドオン投稿を 1 件につき 1 回だけ GET し、アドオン紹介は取得しない", async () => {
    const t = setup({
      "/articles/11/download": [ok("電車.zip")],
      "/articles/22/download": [ok("station.zip")],
    });

    const result = await downloadSequentially([postA, intro, postB], t.options);

    expect(t.calls.map((c) => c.url)).toEqual([
      "/articles/11/download",
      "/articles/22/download",
    ]);
    expect(t.calls.every((c) => c.init?.method === "GET")).toBe(true);
    // 別ホストの URL でも今のオリジンに向ける（CORS で本文を読めず CV だけ増えるのを防ぐ）
    expect(t.calls.every((c) => c.origin === window.location.origin)).toBe(
      true
    );
    expect(t.saveBlob).toHaveBeenCalledTimes(2);
    expect(t.saveBlob.mock.calls[0][1]).toBe("電車.zip");
    expect(t.saveBlob.mock.calls[1][1]).toBe("station.zip");
    expect(result.succeeded.map((i) => i.id)).toEqual([11, 22]);
    expect(result.failed).toEqual([]);
  });

  it("429 のときは Retry-After の秒数だけ待って同じ件を取り直す", async () => {
    const t = setup({
      "/articles/11/download": [tooMany("3"), ok("a.zip")],
      "/articles/22/download": [ok("b.zip")],
    });

    const result = await downloadSequentially([postA, postB], t.options);

    expect(t.calls.map((c) => c.url)).toEqual([
      "/articles/11/download",
      "/articles/11/download",
      "/articles/22/download",
    ]);
    expect(t.sleep).toHaveBeenCalledTimes(3);
    expect(
      t.progress
        .filter((p) => p.type === "waiting")
        .map((p) => (p.type === "waiting" ? p.seconds : 0))
    ).toEqual([3, 2, 1]);
    expect(result.succeeded.map((i) => i.id)).toEqual([11, 22]);
  });

  it("500 は取り直さずに失敗として次の件へ進む", async () => {
    const t = setup({
      "/articles/11/download": [new Response("", { status: 500 })],
      "/articles/22/download": [ok("b.zip")],
    });

    const result = await downloadSequentially([postA, postB], t.options);

    expect(
      t.calls.filter((c) => c.url === "/articles/11/download")
    ).toHaveLength(1);
    expect(result.failed.map((i) => i.id)).toEqual([11]);
    expect(result.succeeded.map((i) => i.id)).toEqual([22]);
    expect(t.sleep).not.toHaveBeenCalled();
  });

  it("通信エラーも取り直さない", async () => {
    const t = setup({
      "/articles/11/download": [new TypeError("network")],
    });

    const result = await downloadSequentially([postA], t.options);

    expect(t.calls).toHaveLength(1);
    expect(result.failed.map((i) => i.id)).toEqual([11]);
  });

  it("本文の読み込みが途中で失敗しても取り直さない", async () => {
    const broken = new Response("", { status: 200 });
    vi.spyOn(broken, "blob").mockRejectedValue(new TypeError("aborted"));
    const t = setup({ "/articles/11/download": [broken] });

    const result = await downloadSequentially([postA], t.options);

    expect(t.calls).toHaveLength(1);
    expect(t.saveBlob).not.toHaveBeenCalled();
    expect(result.failed.map((i) => i.id)).toEqual([11]);
  });

  it("待機中に中止すると残りは取得せず skipped に入る", async () => {
    let cancelled = false;
    const t = setup({ "/articles/11/download": [tooMany("10")] });
    t.sleep.mockImplementation(async (ms: number) => {
      t.clock.ms += ms;
      cancelled = true;
    });

    const result = await downloadSequentially([postA, postB], {
      ...t.options,
      isCancelled: () => cancelled,
    });

    expect(t.calls).toHaveLength(1);
    expect(result.skipped.map((i) => i.id)).toEqual([11, 22]);
    expect(result.succeeded).toEqual([]);
  });

  describe("1 時間の上限で長く待つとき", () => {
    const postC: BulkItem = {
      id: 41,
      title: "貨物ヤード",
      url: "/users/d/yard",
      download_url: "/articles/41/download",
    };
    const postD: BulkItem = {
      id: 52,
      title: "路面電車",
      url: "/users/e/tram",
      download_url: "/articles/52/download",
    };
    const postE: BulkItem = {
      id: 63,
      title: "地下鉄",
      url: "/users/f/subway",
      download_url: "/articles/63/download",
    };

    it("残りは締切の時刻から数え、タイマーが間引かれても予定どおり再開する", async () => {
      const t = setup({
        "/articles/41/download": [tooMany("3600"), ok("c.zip")],
      });
      const start = t.clock.ms;
      // 隠れたタブで 1 回の sleep が 60 秒かかる状況
      t.sleep.mockImplementation(async () => {
        t.clock.ms += 60_000;
      });

      const result = await downloadSequentially([postC], t.options);

      expect(t.sleep).toHaveBeenCalledTimes(60);
      const waiting = t.progress.filter((p) => p.type === "waiting");
      expect(waiting[0]).toMatchObject({
        seconds: 3600,
        totalSeconds: 3600,
        resumeAt: start + 3_600_000,
        index: 0,
        total: 1,
      });
      expect(waiting.at(-1)).toMatchObject({ seconds: 60 });
      expect(result.succeeded.map((i) => i.id)).toEqual([41]);
    });

    it("待機中に中止すると取り直さず、残りを skipped に入れて上限が解ける時刻を返す", async () => {
      let cancelled = false;
      const t = setup({
        "/articles/41/download": [ok("c.zip")],
        "/articles/52/download": [tooMany("3000")],
      });
      const start = t.clock.ms;
      t.sleep.mockImplementation(async (ms: number) => {
        t.clock.ms += ms;
        // 5 分待ったところで中止する
        if (t.clock.ms - start >= 300_000) {
          cancelled = true;
        }
      });

      const result = await downloadSequentially([postC, postD, postE], {
        ...t.options,
        isCancelled: () => cancelled,
      });

      expect(t.calls.map((c) => c.url)).toEqual([
        "/articles/41/download",
        "/articles/52/download",
      ]);
      expect(result.succeeded.map((i) => i.id)).toEqual([41]);
      expect(result.skipped.map((i) => i.id)).toEqual([52, 63]);
      expect(result.failed).toEqual([]);
      expect(result.resumeAt).toBe(start + 3_000_000);
    });

    it("中止せずに終わったときは resumeAt を返さない", async () => {
      const t = setup({
        "/articles/41/download": [tooMany("5"), ok("c.zip")],
      });

      const result = await downloadSequentially([postC], t.options);

      expect(result.resumeAt).toBeUndefined();
    });

    it("中止の後に残りで再開すると、取得済みの件は取り直さず、各件を 1 回だけ保存する", async () => {
      let cancelled = false;
      const t = setup({
        "/articles/41/download": [ok("c.zip")],
        // 1 回目: 上限で中止。2 回目: まだ上限なのでもう一度待ってから取得
        "/articles/52/download": [tooMany("3600"), tooMany("120"), ok("d.zip")],
        "/articles/63/download": [ok("e.zip")],
      });
      t.sleep.mockImplementation(async (ms: number) => {
        t.clock.ms += ms;
        cancelled = true;
      });

      const first = await downloadSequentially([postC, postD, postE], {
        ...t.options,
        isCancelled: () => cancelled,
      });

      // 画面と同じく、取得できなかったもの（skipped）だけで再開する
      cancelled = false;
      t.sleep.mockImplementation(async (ms: number) => {
        t.clock.ms += ms;
      });
      const second = await downloadSequentially(first.skipped, {
        ...t.options,
        isCancelled: () => cancelled,
      });

      expect(t.calls.map((c) => c.url)).toEqual([
        "/articles/41/download",
        "/articles/52/download",
        "/articles/52/download",
        "/articles/52/download",
        "/articles/63/download",
      ]);
      // 200 を返した（= CV を数えた）取得は各件 1 回だけ
      expect(t.saveBlob.mock.calls.map((c) => c[1])).toEqual([
        "c.zip",
        "d.zip",
        "e.zip",
      ]);
      expect(second.succeeded.map((i) => i.id)).toEqual([52, 63]);
      expect(second.skipped).toEqual([]);
    });
  });
});

describe("isLongWait", () => {
  it("1 分の上限の待ち（60 秒以下）は短く、2 分以上は長い", () => {
    expect(isLongWait(60)).toBe(false);
    expect(isLongWait(LONG_WAIT_SECONDS - 1)).toBe(false);
    expect(isLongWait(LONG_WAIT_SECONDS)).toBe(true);
    expect(isLongWait(3600)).toBe(true);
  });
});

describe("parseRetryAfter", () => {
  it("秒数と HTTP 日付を秒にする", () => {
    expect(parseRetryAfter("42")).toBe(42);
    const now = Date.parse("2026-11-11T00:00:00Z");
    expect(parseRetryAfter("Wed, 11 Nov 2026 00:00:30 GMT", now)).toBe(30);
  });

  it("読めない値は既定の秒数", () => {
    expect(parseRetryAfter(null)).toBe(DEFAULT_RETRY_AFTER_SECONDS);
    expect(parseRetryAfter("soon")).toBe(DEFAULT_RETRY_AFTER_SECONDS);
  });
});

describe("fileNameFromDisposition", () => {
  it("filename* を優先し、無ければ filename、どちらも無ければ代わりの名前", () => {
    expect(
      fileNameFromDisposition(
        "attachment; filename=a.zip; filename*=utf-8''%E9%A7%85.zip",
        "x"
      )
    ).toBe("駅.zip");
    expect(fileNameFromDisposition('attachment; filename="b.zip"', "x")).toBe(
      "b.zip"
    );
    expect(fileNameFromDisposition(null, "代わり")).toBe("代わり");
  });
});
