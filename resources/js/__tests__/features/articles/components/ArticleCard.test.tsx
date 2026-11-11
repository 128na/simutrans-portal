import { render, screen, within } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import {
  ArticleCard,
  isDownloadable,
} from "@/features/articles/components/ArticleCard";
import { ArticleCardList } from "@/features/articles/components/ArticleCardList";

vi.mock("@/features/mylist/AddToMyList", () => ({
  AddToMyListButton: ({ articleId }: { articleId: number }) => (
    <button type="button">マイリスト {articleId}</button>
  ),
}));

const post: Article.Card = {
  id: 101,
  title: "通勤電車セット",
  url: "/users/alice/commuter",
  post_type: "addon-post",
  download_url: "/articles/101/download",
  thumbnail: "/thumb/101.png",
  categories: [
    { id: 11, type: "pak", slug: "128-japan" },
    { id: 31, type: "addon", slug: "trains" },
    { id: 51, type: "license", slug: "cc-by" },
  ],
  user: { id: 7, name: "アリス", url: "/users/alice" },
  published_at: "2026/01/03 10:00",
  modified_at: "2026/01/04 12:00",
};

const intro: Article.Card = {
  id: 202,
  title: "高架駅ホーム",
  url: "/users/bob/platform",
  post_type: "addon-introduction",
  addon_page_url: "/articles/202/conversion",
  thumbnail: "/thumb/202.png",
  categories: [
    { id: 12, type: "pak", slug: "64" },
    { id: 32, type: "addon", slug: "rail-tools" },
  ],
  user: { id: 8, name: "ボブ", url: "/users/bob" },
  published_at: "2026/02/01 09:00",
  modified_at: "2026/02/05 09:00",
};

describe("アドオン一覧のカード", () => {
  it("サムネイルのリンクに記事名を付け、タイトル・pak・種類・作者・更新日を表示する", () => {
    render(<ArticleCard article={post} />);

    const links = screen.getAllByRole("link", { name: "通勤電車セット" });
    expect(links).toHaveLength(2);
    links.forEach((link) =>
      expect(link).toHaveAttribute("href", "/users/alice/commuter")
    );
    // 縦長のサムネイルでもカードの高さがそろうよう、16:9 の枠からはみ出させない
    expect(links[0]).toHaveClass("aspect-video", "overflow-hidden");
    expect(screen.getByRole("link", { name: "Pak128.Japan" })).toHaveClass(
      "v2-tag-pak-128-japan"
    );
    expect(screen.getByRole("link", { name: "鉄道車両" })).toHaveClass(
      "v2-category"
    );
    expect(
      screen.queryByRole("link", { name: /cc-by|CC/ })
    ).not.toBeInTheDocument();
    expect(screen.getByRole("link", { name: "アリス" })).toHaveAttribute(
      "href",
      "/users/alice"
    );
    expect(screen.getByText(/更新 2026\/01\/04/)).toBeInTheDocument();
  });

  it("1 行に収めたタグの行で隠れたタグにフォーカスしたら見える位置まで動かす", () => {
    const scrollIntoView = vi.fn();
    Element.prototype.scrollIntoView = scrollIntoView;
    render(<ArticleCard article={post} />);

    const tag = screen.getByRole("link", { name: "鉄道車両" });
    tag.focus();

    expect(scrollIntoView).toHaveBeenCalledTimes(1);
    expect(scrollIntoView.mock.contexts[0]).toBe(tag);
    // @ts-expect-error jsdom には無いメソッドを元に戻す
    delete Element.prototype.scrollIntoView;
  });

  it("作者名が長くても作者・日付の行は 1 行に収め、日付は折り返さない", () => {
    render(
      <ArticleCard
        article={{
          ...post,
          user: { ...post.user, name: "とても長い作者名".repeat(5) },
        }}
      />
    );

    const author = screen.getByRole("link", { name: /とても長い作者名/ });
    expect(author).toHaveClass("truncate", "min-w-0");
    expect(screen.getByText(/更新 2026\/01\/04/)).toHaveClass(
      "whitespace-nowrap",
      "shrink-0"
    );
  });

  it("タイトルは英単語の途中で折らず、1 語が長すぎるときだけ途中で折ってカードからはみ出させない", () => {
    render(
      <ArticleCard
        article={{ ...post, title: "Frankfurt (am Main) U-Bahn U2 & U3" }}
      />
    );

    const [, titleLink] = screen.getAllByRole("link", {
      name: "Frankfurt (am Main) U-Bahn U2 & U3",
    });
    // jsdom では折り返しを測れないので、上書きのクラスで確かめる（実測は PR #665）
    expect(titleLink).toHaveClass("[word-break:normal]!", "wrap-anywhere");
  });

  it("公開順のときは公開日を表示する", () => {
    render(<ArticleCard article={post} sort="published" />);

    expect(screen.getByText(/公開 2026\/01\/03/)).toBeInTheDocument();
    expect(screen.queryByText(/更新 2026/)).not.toBeInTheDocument();
  });

  it("アドオン投稿は既存のダウンロード経路へのボタンを出す", () => {
    render(<ArticleCard article={post} />);

    const download = screen.getByRole("link", {
      name: "通勤電車セット をダウンロード",
    });
    expect(download).toHaveAttribute("href", "/articles/101/download");
    expect(download).toHaveAttribute("target", "_blank");
    expect(screen.queryByText("掲載先で入手")).not.toBeInTheDocument();
    expect(isDownloadable(post)).toBe(true);
  });

  it("アドオン紹介は掲載先で入手することが分かり、既存の掲載先経路へ移動する", () => {
    render(<ArticleCard article={intro} />);

    // タグの行は 1 行に収めてあふれた分を隠すので、入手方法の目印は先頭に置く
    expect(screen.getByText("掲載先で入手")).toBe(
      screen.getByText("掲載先で入手").parentElement?.firstElementChild
    );
    expect(
      screen.getByRole("link", { name: "高架駅ホーム の掲載先へ" })
    ).toHaveAttribute("href", "/articles/202/conversion");
    expect(
      screen.queryByRole("link", { name: /ダウンロード/ })
    ).not.toBeInTheDocument();
    expect(isDownloadable(intro)).toBe(false);
  });

  it("検索結果の一般記事は入手方法の表示も入手ボタンも出さない", () => {
    render(
      <ArticleCard
        article={{
          ...intro,
          id: 303,
          post_type: "page",
          addon_page_url: undefined,
        }}
      />
    );

    expect(screen.queryByText("掲載先で入手")).not.toBeInTheDocument();
    expect(
      screen.queryByRole("link", { name: /掲載先へ|ダウンロード/ })
    ).not.toBeInTheDocument();
  });

  it("ログイン中だけマイリストのボタンを出し、操作欄に要素を足せる", () => {
    const { rerender } = render(<ArticleCard article={post} />);
    expect(
      screen.queryByRole("button", { name: /マイリスト/ })
    ).not.toBeInTheDocument();

    rerender(
      <ArticleCard
        article={post}
        isAuthenticated
        actions={<button type="button">選ぶ</button>}
      />
    );
    expect(
      screen.getByRole("button", { name: "マイリスト 101" })
    ).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "選ぶ" })).toBeInTheDocument();
  });
});

describe("アドオン一覧のカード一覧", () => {
  it("記事ごとにカードを並べ、操作欄の要素を記事ごとに作れる", () => {
    render(
      <ArticleCardList
        articles={[post, intro]}
        renderActions={(article) => (
          <button type="button">{`選ぶ ${article.id}`}</button>
        )}
      />
    );

    const cards = screen.getAllByRole("article");
    expect(cards).toHaveLength(2);
    expect(
      within(cards[0]).getByRole("button", { name: "選ぶ 101" })
    ).toBeInTheDocument();
    expect(
      within(cards[1]).getByRole("button", { name: "選ぶ 202" })
    ).toBeInTheDocument();
  });

  it("記事がなければ条件に合う記事がないと伝える", () => {
    render(<ArticleCardList articles={[]} />);

    expect(
      screen.getByText("条件に合う記事が見つかりませんでした。")
    ).toBeInTheDocument();
  });
});
