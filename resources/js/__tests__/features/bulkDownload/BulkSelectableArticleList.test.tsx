import { act, render, screen, waitFor, within } from "@testing-library/react";
import { Profiler } from "react";
import userEvent from "@testing-library/user-event";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import { BulkSelectableArticleList } from "@/features/bulkDownload/BulkSelectableArticleList";
import { SELECTION_STORAGE_KEY } from "@/features/bulkDownload/useBulkSelection";
import type {
  BulkItem,
  BulkResult,
  downloadSequentially,
} from "@/features/bulkDownload/bulkDownload";

vi.mock("@/features/mylist/AddToMyList", () => ({
  AddToMyListButton: () => null,
  AddToMyListModal: ({ articleIds }: { articleIds: number[] }) => (
    <div role="dialog">保存対象 {articleIds.join(",")}</div>
  ),
}));

const card = (overrides: Partial<Article.Card>): Article.Card => ({
  id: 0,
  title: "",
  url: "/users/x/y",
  post_type: "addon-post",
  thumbnail: "/thumb.png",
  categories: [],
  user: { id: 9, name: "作者", url: "/users/x" },
  published_at: "2026/01/01 00:00",
  modified_at: null,
  ...overrides,
});

const post = card({
  id: 101,
  title: "通勤電車セット",
  download_url: "/articles/101/download",
});
const post2 = card({
  id: 102,
  title: "特急電車セット",
  download_url: "/articles/102/download",
});
const intro = card({
  id: 202,
  title: "高架駅ホーム",
  post_type: "addon-introduction",
  addon_page_url: "/articles/202/conversion",
});
const page = card({ id: 303, title: "遊び方の解説", post_type: "page" });

type Download = typeof downloadSequentially;

const resolvedDownload = (result: Partial<BulkResult>) =>
  vi.fn<Download>(async (items: BulkItem[]) => ({
    succeeded: items.filter((i) => i.download_url),
    failed: [],
    skipped: [],
    ...result,
  }));

describe("BulkSelectableArticleList", () => {
  beforeEach(() => {
    window.sessionStorage.clear();
  });
  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("アドオン投稿とアドオン紹介だけに「選ぶ」を出し、一般記事には出さない", () => {
    render(<BulkSelectableArticleList articles={[post, intro, page]} />);

    expect(screen.getByLabelText("通勤電車セット を選ぶ")).toBeInTheDocument();
    expect(screen.getByLabelText("高架駅ホーム を選ぶ")).toBeInTheDocument();
    expect(screen.queryByLabelText("遊び方の解説 を選ぶ")).toBeNull();
  });

  it("選ぶと件数と内訳を出し、sessionStorage に残してページ送り後も復元する", async () => {
    const user = userEvent.setup();
    const { unmount } = render(
      <BulkSelectableArticleList articles={[post, intro]} />
    );

    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));
    await user.click(screen.getByLabelText("高架駅ホーム を選ぶ"));

    expect(screen.getByText("2 件を選択中")).toBeInTheDocument();
    expect(
      screen.getByText(/ダウンロード 1 件 ・ 掲載先で入手 1 件/)
    ).toBeInTheDocument();
    const stored = JSON.parse(
      window.sessionStorage.getItem(SELECTION_STORAGE_KEY) ?? "[]"
    );
    expect(stored.map((i: BulkItem) => i.id)).toEqual([101, 202]);

    unmount();
    // 次のページ（別の記事）を開いても選択が残る
    render(<BulkSelectableArticleList articles={[post2]} />);
    expect(screen.getByText("2 件を選択中")).toBeInTheDocument();
  });

  it("選択の帯は一覧の後ろに置いて画面の下端に貼り付け、一覧を押し下げない", async () => {
    const user = userEvent.setup();
    render(<BulkSelectableArticleList articles={[post, intro]} />);

    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));

    const band = screen.getByRole("region", { name: "選択中のアドオン" });
    const lastCard = screen.getAllByRole("article").at(-1);
    expect(lastCard).toBeDefined();
    expect(
      lastCard!.compareDocumentPosition(band) & Node.DOCUMENT_POSITION_FOLLOWING
    ).toBeTruthy();
    expect(band.parentElement).toHaveClass("sticky", "bottom-0");
  });

  it("すべて取得して選択が空になっても、結果は一覧の後ろの下端に出す", async () => {
    const user = userEvent.setup();
    render(
      <BulkSelectableArticleList
        articles={[post, post2]}
        download={resolvedDownload({})}
      />
    );
    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));
    await user.click(
      screen.getByRole("button", { name: "まとめてダウンロード" })
    );

    const resultArea = await screen.findByRole("status", {
      name: "まとめてダウンロードの結果",
    });
    expect(
      screen.queryByRole("region", { name: "選択中のアドオン" })
    ).toBeNull();
    const lastCard = screen.getAllByRole("article").at(-1);
    expect(
      lastCard!.compareDocumentPosition(resultArea) &
        Node.DOCUMENT_POSITION_FOLLOWING
    ).toBeTruthy();
    expect(resultArea.parentElement).toHaveClass("sticky", "bottom-0");
  });

  it("sessionStorage が使えなくても選択できる", async () => {
    vi.spyOn(Storage.prototype, "getItem").mockImplementation(() => {
      throw new Error("denied");
    });
    vi.spyOn(Storage.prototype, "setItem").mockImplementation(() => {
      throw new Error("denied");
    });
    const user = userEvent.setup();
    render(<BulkSelectableArticleList articles={[post]} />);

    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));

    expect(screen.getByText("1 件を選択中")).toBeInTheDocument();
  });

  it("まとめてダウンロードは連打しても 1 回だけ実行し、終わったら掲載先リンクを一覧にする", async () => {
    let finish: (r: BulkResult) => void = () => {};
    const download = vi.fn<Download>(
      () =>
        new Promise<BulkResult>((resolve) => {
          finish = resolve;
        })
    );
    const user = userEvent.setup();
    render(
      <BulkSelectableArticleList articles={[post, intro]} download={download} />
    );
    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));
    await user.click(screen.getByLabelText("高架駅ホーム を選ぶ"));

    const button = screen.getByRole("button", { name: "まとめてダウンロード" });
    await user.click(button);
    expect(
      screen.queryByRole("button", { name: "まとめてダウンロード" })
    ).toBeNull();
    expect(download).toHaveBeenCalledTimes(1);
    expect(download.mock.calls[0][0].map((i) => i.id)).toEqual([101, 202]);

    finish({
      succeeded: [{ id: 101, title: "通勤電車セット", url: post.url }],
      failed: [],
      skipped: [],
    });

    const resultArea = await screen.findByRole("status", {
      name: "まとめてダウンロードの結果",
    });
    expect(
      within(resultArea).getByText("1 件をダウンロードしました")
    ).toBeInTheDocument();
    expect(
      within(resultArea).getByText(/掲載先で入手するもの 1/)
    ).toBeInTheDocument();
    const link = within(resultArea).getByRole("link", { name: /高架駅ホーム/ });
    expect(link).toHaveAttribute("href", "/articles/202/conversion");
    expect(link).toHaveAttribute("target", "_blank");
    // 取得し終えたアドオン投稿は選択から外し、アドオン紹介は残す
    expect(screen.getByText("1 件を選択中")).toBeInTheDocument();
    expect(
      screen.getByLabelText("高架駅ホーム を選択から外す")
    ).toBeInTheDocument();
    expect(screen.getByLabelText("通勤電車セット を選ぶ")).toBeInTheDocument();
  });

  it("取得できなかったものだけ選択に残す", async () => {
    const download = resolvedDownload({
      succeeded: [{ id: 101, title: "通勤電車セット", url: post.url }],
      failed: [{ id: 102, title: "特急電車セット", url: post2.url }],
    });
    const user = userEvent.setup();
    render(
      <BulkSelectableArticleList articles={[post, post2]} download={download} />
    );
    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));
    await user.click(screen.getByLabelText("特急電車セット を選ぶ"));
    await user.click(
      screen.getByRole("button", { name: "まとめてダウンロード" })
    );

    await waitFor(() =>
      expect(screen.getByText("1 件を選択中")).toBeInTheDocument()
    );
    expect(
      screen.getByLabelText("特急電車セット を選択から外す")
    ).toBeInTheDocument();
    expect(
      screen.getByText(/件はダウンロードできませんでした/)
    ).toBeInTheDocument();
  });

  describe("ダウンロードの上限で待つとき", () => {
    const yard = card({
      id: 114,
      title: "貨物ヤード",
      download_url: "/articles/114/download",
    });
    const tram = card({
      id: 125,
      title: "路面電車",
      download_url: "/articles/125/download",
    });
    const subway = card({
      id: 136,
      title: "地下鉄",
      download_url: "/articles/136/download",
    });
    const now = Date.parse("2026-11-11T05:00:00Z");
    const resumeAt = Date.parse("2026-11-11T05:47:00Z");
    beforeEach(() => {
      // 「明日」の判定を決まった日付で行う。タイマーは本物のまま（userEvent が使う）
      vi.useFakeTimers({ toFake: ["Date"] });
      vi.setSystemTime(now);
    });
    afterEach(() => {
      vi.useRealTimers();
    });
    const clock = (epochMs: number) =>
      new Date(epochMs).toLocaleTimeString("ja-JP", {
        hour: "2-digit",
        minute: "2-digit",
      });

    /** 1 件目を取得した後、2 件目で待機したまま止まるダウンロード */
    const waitingDownload = (
      totalSeconds: number,
      seconds: number,
      waitUntil: number = resumeAt
    ) => {
      let finish: (r: BulkResult) => void = () => {};
      let isCancelled: () => boolean = () => false;
      let emit: (seconds: number) => void = () => {};
      const download = vi.fn<Download>((items, options) => {
        isCancelled = options?.isCancelled ?? isCancelled;
        emit = (s: number) =>
          options?.onProgress?.({
            type: "waiting",
            seconds: s,
            totalSeconds,
            resumeAt: waitUntil,
            index: 1,
            total: items.length,
            item: items[1],
          });
        emit(seconds);
        return new Promise<BulkResult>((resolve) => {
          finish = resolve;
        });
      });
      return {
        download,
        finish: (r: BulkResult) => finish(r),
        isCancelled: () => isCancelled(),
        emit: (s: number) => act(() => emit(s)),
      };
    };

    const selectAll = async (user: ReturnType<typeof userEvent.setup>) => {
      await user.click(screen.getByLabelText("貨物ヤード を選ぶ"));
      await user.click(screen.getByLabelText("路面電車 を選ぶ"));
      await user.click(screen.getByLabelText("地下鉄 を選ぶ"));
      await user.click(
        screen.getByRole("button", { name: "まとめてダウンロード" })
      );
    };

    it("1 時間の上限では待ち時間を分と時刻で示し、中止して後で取得する操作を前面に出す", async () => {
      const w = waitingDownload(2820, 2790);
      const user = userEvent.setup();
      render(
        <BulkSelectableArticleList
          articles={[yard, tram, subway]}
          download={w.download}
        />
      );
      await selectAll(user);

      const band = screen.getByRole("region", { name: "選択中のアドオン" });
      const status = within(band).getByRole("status");
      // 2790 秒 → 約 47 分（切り上げ）
      expect(status).toHaveTextContent(
        `再開まで約 47 分（${clock(resumeAt)} ごろ）`
      );
      expect(status).toHaveTextContent("未取得の 2 件は選択に残ります");
      expect(status).not.toHaveTextContent("秒");
      const cancel = within(band).getByRole("button", {
        name: "中止して後で取得する",
      });
      expect(cancel).toHaveClass("v2-button");
      expect(within(band).queryByRole("button", { name: "中止" })).toBeNull();
      // 保存の注意は出さず、帯を低く保つ
      expect(within(band).queryByText(/複数ファイルのダウンロード/)).toBeNull();

      expect(w.isCancelled()).toBe(false);
      await user.click(cancel);
      expect(w.isCancelled()).toBe(true);
    });

    it("長い待ちは分が変わるときだけ描き直す", async () => {
      const w = waitingDownload(2820, 2790);
      const onRender = vi.fn();
      const user = userEvent.setup();
      render(
        <Profiler id="list" onRender={onRender}>
          <BulkSelectableArticleList
            articles={[yard, tram, subway]}
            download={w.download}
          />
        </Profiler>
      );
      await selectAll(user);
      const status = within(
        screen.getByRole("region", { name: "選択中のアドオン" })
      ).getByRole("status");
      const commits = onRender.mock.calls.length;

      // 2789〜2761 秒はどれも約 47 分
      for (let s = 2789; s > 2760; s--) {
        w.emit(s);
      }
      // React は同じ値への更新でも 1 回だけ描き直すことがある。毎秒描き直すなら 29 回増える
      expect(onRender.mock.calls.length).toBeLessThanOrEqual(commits + 1);
      expect(status).toHaveTextContent("再開まで約 47 分");

      w.emit(2760);
      expect(status).toHaveTextContent("再開まで約 46 分");
    });

    it("再開が日付をまたぐときは「明日」を付ける", async () => {
      const tomorrow = now + 24 * 60 * 60 * 1000;
      const w = waitingDownload(3600, 3600, tomorrow);
      const user = userEvent.setup();
      render(
        <BulkSelectableArticleList
          articles={[yard, tram, subway]}
          download={w.download}
        />
      );
      await selectAll(user);

      expect(
        within(
          screen.getByRole("region", { name: "選択中のアドオン" })
        ).getByRole("status")
      ).toHaveTextContent(`（明日 ${clock(tomorrow)} ごろ）`);
    });

    it("1 分の上限の短い待ちは秒で数え、中止はいつもどおり控えめに出す", async () => {
      const w = waitingDownload(42, 42);
      const user = userEvent.setup();
      render(
        <BulkSelectableArticleList
          articles={[yard, tram, subway]}
          download={w.download}
        />
      );
      await selectAll(user);

      const band = screen.getByRole("region", { name: "選択中のアドオン" });
      expect(within(band).getByRole("status")).toHaveTextContent(
        "あと 42 秒で「路面電車」から再開します"
      );
      expect(
        within(band).getByRole("button", { name: "中止" })
      ).not.toHaveClass("v2-button");
      expect(
        within(band).queryByRole("button", { name: "中止して後で取得する" })
      ).toBeNull();
    });

    it("長い待ちで中止すると、再開できる時刻を案内し、未取得分だけで続きから取得できる", async () => {
      const w = waitingDownload(3600, 3500);
      const user = userEvent.setup();
      render(
        <BulkSelectableArticleList
          articles={[yard, tram, subway]}
          download={w.download}
          isAuthenticated
        />
      );
      await selectAll(user);
      await user.click(
        screen.getByRole("button", { name: "中止して後で取得する" })
      );
      const [, tramItem, subwayItem] = w.download.mock.calls[0][0];
      w.finish({
        succeeded: [w.download.mock.calls[0][0][0]],
        failed: [],
        skipped: [tramItem, subwayItem],
        resumeAt,
      });

      const resultArea = await screen.findByRole("status", {
        name: "まとめてダウンロードの結果",
      });
      expect(resultArea).toHaveTextContent(
        "中止したため 2 件はダウンロードしていません（選択に残しています）。"
      );
      expect(resultArea).toHaveTextContent(
        `${clock(resumeAt)} 以降に「まとめてダウンロード」を押すと続きから取得できます`
      );
      expect(resultArea).toHaveTextContent("マイリストに保存しておけます");
      expect(screen.getByText("2 件を選択中")).toBeInTheDocument();
      const stored = JSON.parse(
        window.sessionStorage.getItem(SELECTION_STORAGE_KEY) ?? "[]"
      );
      expect(stored.map((i: BulkItem) => i.id)).toEqual([125, 136]);

      // 再開は取得済みの件を含めない（同じ件の CV を二重に数えない）
      await user.click(
        screen.getByRole("button", { name: "まとめてダウンロード" })
      );
      expect(w.download).toHaveBeenCalledTimes(2);
      expect(w.download.mock.calls[1][0].map((i) => i.id)).toEqual([125, 136]);
    });

    it("待機せずに中止したときは再開の時刻を出さない", async () => {
      const download = resolvedDownload({
        succeeded: [],
        skipped: [{ id: 114, title: "貨物ヤード", url: yard.url }],
      });
      const user = userEvent.setup();
      render(
        <BulkSelectableArticleList articles={[yard]} download={download} />
      );
      await user.click(screen.getByLabelText("貨物ヤード を選ぶ"));
      await user.click(
        screen.getByRole("button", { name: "まとめてダウンロード" })
      );

      const resultArea = await screen.findByRole("status", {
        name: "まとめてダウンロードの結果",
      });
      expect(resultArea).toHaveTextContent("中止したため 1 件");
      expect(resultArea).not.toHaveTextContent("以降に");
    });
  });

  it("未ログインではマイリスト保存の代わりにログインへの導線を出す", async () => {
    const user = userEvent.setup();
    render(<BulkSelectableArticleList articles={[post]} />);
    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));

    expect(
      screen.getByRole("link", { name: "ログインしてマイリストに保存" })
    ).toHaveAttribute("href", "/login");
    expect(
      screen.queryByRole("button", { name: "マイリストに保存" })
    ).toBeNull();
  });

  it("ログイン中は選択中の記事をまとめてマイリストに保存できる", async () => {
    const user = userEvent.setup();
    render(
      <BulkSelectableArticleList articles={[post, intro]} isAuthenticated />
    );
    await user.click(screen.getByLabelText("通勤電車セット を選ぶ"));
    await user.click(screen.getByLabelText("高架駅ホーム を選ぶ"));
    await user.click(screen.getByRole("button", { name: "マイリストに保存" }));

    expect(screen.getByRole("dialog")).toHaveTextContent("保存対象 101,202");
  });
});
