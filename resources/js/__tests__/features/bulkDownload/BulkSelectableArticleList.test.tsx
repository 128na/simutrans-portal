import { render, screen, waitFor, within } from "@testing-library/react";
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
    expect(band).toHaveClass("sticky", "bottom-0");
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
