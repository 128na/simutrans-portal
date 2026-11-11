import { render, screen, within } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { LatestArticleCardList } from "@/features/articles/components/LatestArticleCardList";
import { ArticleCard } from "@/features/articles/components/ArticleCard";

vi.mock("@/features/mylist/AddToMyList", () => ({
  AddToMyListButton: ({ articleId }: { articleId: number }) => (
    <button type="button">マイリスト {articleId}</button>
  ),
}));

const updated: Article.LatestCard = {
  id: 401,
  title: "更新された車両",
  url: "/users/carol/updated",
  post_type: "addon-post",
  download_url: "/articles/401/download",
  thumbnail: "/thumb/401.png",
  categories: [
    { id: 13, type: "pak", slug: "128" },
    { id: 33, type: "addon", slug: "trains" },
  ],
  user: { id: 9, name: "キャロル", url: "/users/carol" },
  published_at: "2026/03/01 10:00",
  modified_at: "2026/03/05 18:30",
  is_updated: true,
};

const created: Article.LatestCard = {
  id: 502,
  title: "新しい駅舎",
  url: "/users/dave/created",
  post_type: "addon-introduction",
  addon_page_url: "/articles/502/conversion",
  thumbnail: "/thumb/502.png",
  categories: [{ id: 14, type: "pak", slug: "64" }],
  user: { id: 10, name: "デイブ", url: "/users/dave" },
  published_at: "2026/04/02 09:00",
  modified_at: "2026/04/02 09:00",
  is_updated: false,
};

/** 追加項目の無い保存データから作ったカード（投稿形式・作者・タグが無い） */
const legacy: Article.LatestCard = {
  id: 603,
  title: "古い保存データの記事",
  url: "/users/erin/legacy",
  post_type: null,
  thumbnail: "/thumb/603.png",
  categories: [],
  user: null,
  published_at: "2026/05/03 08:00",
  modified_at: "2026/05/03 08:00",
  is_updated: false,
};

describe("新着カード", () => {
  it("一覧ページと同じカード・列数で並べる", () => {
    const { container } = render(
      <LatestArticleCardList articles={[updated, created]} />
    );

    const cards = screen.getAllByRole("article");
    expect(cards).toHaveLength(2);
    // 一覧ページ（ArticleCardList）と同じ列数。スマホ幅は 1 列
    expect(container.firstElementChild).toHaveClass(
      "grid",
      "grid-cols-1",
      "sm:grid-cols-2",
      "lg:grid-cols-3",
      "xl:grid-cols-4"
    );
    // 一覧のカードと同じ部品なので、同じ記事なら新着だけの見出し以外は同じ HTML になる
    const { container: listCard } = render(
      <ArticleCard
        article={{ ...updated, post_type: "addon-post" }}
        sort="published"
      />
    );
    const latestCard = cards[0].cloneNode(true) as HTMLElement;
    latestCard.querySelector(".p-2\\.5")?.firstElementChild?.remove();
    expect(latestCard.outerHTML).toBe(
      listCard.querySelector("article")?.outerHTML
    );
  });

  it("更新はタイトルの上に「更新」と更新日、カード下に公開日を出す", () => {
    render(<LatestArticleCardList articles={[updated]} />);

    const card = screen.getByRole("article");
    const label = within(card).getByText("更新");
    const heading = label.parentElement as HTMLElement;
    expect(within(heading).getByText("2026/03/05")).toHaveAttribute(
      "datetime",
      "2026-03-05 18:30"
    );
    // サムネイルには重ねず、タイトルの前（上の行）に置く
    const title = within(card).getAllByRole("link", {
      name: "更新された車両",
    })[1];
    expect(
      heading.compareDocumentPosition(title) & Node.DOCUMENT_POSITION_FOLLOWING
    ).toBeTruthy();
    expect(
      within(card).getAllByRole("link", { name: "更新された車両" })[0]
    ).not.toContainElement(label);
    expect(within(card).getByText(/公開 2026\/03\/01/)).toBeInTheDocument();
    expect(within(card).queryByText("新規")).not.toBeInTheDocument();
  });

  it("新規は「新規」だけを見出しに出し、日付はカード下の公開日 1 つにする", () => {
    render(<LatestArticleCardList articles={[created]} />);

    const card = screen.getByRole("article");
    const label = within(card).getByText("新規");
    expect(label.parentElement?.querySelector("time")).toBeNull();
    expect(within(card).getAllByText(/2026\/04\/02/)).toHaveLength(1);
    expect(within(card).getByText(/公開 2026\/04\/02/)).toBeInTheDocument();
    expect(within(card).queryByText("更新")).not.toBeInTheDocument();
  });

  it("入手の操作は一覧と同じ CV 経路で、スマホ幅では出さない。まとめてダウンロードの「選ぶ」は出さない", () => {
    render(<LatestArticleCardList articles={[updated, created]} />);

    const download = screen.getByRole("link", {
      name: "更新された車両 をダウンロード",
    });
    expect(download).toHaveAttribute("href", "/articles/401/download");
    expect(download).toHaveClass("hidden", "sm:inline-block");
    const external = screen.getByRole("link", {
      name: "新しい駅舎 の掲載先へ",
    });
    expect(external).toHaveAttribute("href", "/articles/502/conversion");
    expect(external).toHaveClass("hidden", "sm:inline-block");
    expect(
      screen.queryByRole("button", { name: /選ぶ/ })
    ).not.toBeInTheDocument();
  });

  it("ログイン中は一覧と同じくマイリストのボタンを出す", () => {
    render(<LatestArticleCardList articles={[created]} isAuthenticated />);

    expect(
      screen.getByRole("button", { name: "マイリスト 502" })
    ).toBeInTheDocument();
  });

  it("古い保存データのカードは、作者・タグ・入手の操作だけを省いて出す", () => {
    render(<LatestArticleCardList articles={[legacy]} />);

    const card = screen.getByRole("article");
    expect(
      within(card).getAllByRole("link", { name: "古い保存データの記事" })
    ).toHaveLength(2);
    expect(within(card).getAllByRole("link")).toHaveLength(2);
    expect(within(card).getByText("公開 2026/05/03")).toBeInTheDocument();
    expect(within(card).queryByText(/・/)).not.toBeInTheDocument();
  });
});
