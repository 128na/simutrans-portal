import { describe, expect, it, vi } from "vitest";
import {
  DEFAULT_RETRY_AFTER_SECONDS,
  downloadSequentially,
  fileNameFromDisposition,
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
  download_url: "/articles/22/download",
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
  const calls: Array<{ url: string; init?: RequestInit }> = [];
  const fetchImpl = vi.fn(
    async (input: RequestInfo | URL, init?: RequestInit) => {
      const url = String(input);
      calls.push({ url, init });
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
  const sleep = vi.fn(async () => {});
  const progress: BulkProgress[] = [];
  return {
    calls,
    fetchImpl,
    saveBlob,
    sleep,
    progress,
    options: {
      fetchImpl,
      saveBlob,
      sleep,
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
    t.sleep.mockImplementation(async () => {
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
