import { render, screen, within } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { ArticleBase } from "@/features/articles/components/ArticleBase";

/**
 * 記事詳細の入手ボックス（#639）。
 * 入手に必要な情報をボックスにまとめても、リンク先は #621 で整えた計測経路のままにする。
 */
const baseArticle = {
  id: 5,
  title: "テストアドオン",
  slug: "test-addon",
  user: {
    id: 1,
    name: "投稿者",
    nickname: null,
    profile: {
      id: 2,
      data: { avatar: null, description: null, website: [] },
      attachments: [],
    },
  },
  categories: [
    { id: 11, type: "pak", slug: "128-japan" },
    { id: 12, type: "addon", slug: "trains" },
    { id: 13, type: "license", slug: "cc-by" },
  ],
  tags: [{ id: 21, name: "テストタグ" }],
  articles: [],
  relatedArticles: [],
  published_at: "2022/11/08 10:00",
  modified_at: "2026/06/18 12:30",
  attachments: [],
} as const;

const addonPost = {
  ...baseArticle,
  categories: [...baseArticle.categories],
  tags: [...baseArticle.tags],
  articles: [],
  relatedArticles: [],
  post_type: "addon-post",
  download_url: "/articles/5/download",
  attachments: [
    {
      id: 3,
      thumbnail: "",
      original_name: "addon.zip",
      url: "/storage/addon.zip",
      size: 1024,
    },
  ],
  contents: {
    thumbnail: null,
    description: "本文",
    file: 3,
    author: "作者E",
    license: "改変自由",
    thanks: null,
  },
} satisfies Article.Show;

const addonIntroduction = {
  ...baseArticle,
  categories: [...baseArticle.categories],
  tags: [...baseArticle.tags],
  articles: [],
  relatedArticles: [],
  post_type: "addon-introduction",
  addon_page_url: "/articles/5/conversion",
  contents: {
    thumbnail: null,
    description: "本文",
    link: "https://example.org/addon.zip",
    link_type: "page",
    author: "",
    license: null,
    thanks: null,
    agreement: true,
    exclude_link_check: false,
  },
} satisfies Article.Show;

const acquireBox = () => screen.getByRole("region", { name: "アドオンの入手" });

describe("記事詳細の入手ボックス", () => {
  it("投稿記事：ボタン・ファイル・マイリスト・pak・ライセンス・作者をボックスにまとめ、ダウンロード経路を使う", () => {
    render(<ArticleBase article={addonPost} isAuthenticated />);
    const box = within(acquireBox());

    expect(
      box.getByRole("link", { name: "アドオンをダウンロード" })
    ).toHaveAttribute("href", "/articles/5/download");
    expect(box.getByText(/addon\.zip/)).toBeInTheDocument();
    expect(
      box.getByRole("button", { name: "マイリストに追加" })
    ).toBeInTheDocument();
    // タイトル直下には重ねて出さない
    expect(
      screen.getAllByRole("button", { name: "マイリストに追加" })
    ).toHaveLength(1);
    expect(box.getByRole("link", { name: "Pak128.Japan" })).toHaveAttribute(
      "href",
      "/search?categoryIds[]=11"
    );
    expect(box.getByRole("link", { name: "CC BY" })).toHaveAttribute(
      "href",
      "/search?categoryIds[]=13"
    );
    expect(box.getByRole("link", { name: "利用条件を見る" })).toHaveAttribute(
      "href",
      "#addon-license"
    );
    expect(box.getByText("作者E")).toBeInTheDocument();
    expect(box.queryByText("掲載許可")).not.toBeInTheDocument();
    // 利用条件の本文はリンク先として残す
    expect(document.getElementById("addon-license")).toHaveTextContent(
      "改変自由"
    );
    // 作者・日時の表はボックスとタイトル直下へ移したので出さない
    expect(screen.queryByText("詳細情報")).not.toBeInTheDocument();
  });

  it("紹介記事：外部リンクは計測経路を通し、掲載許可と投稿者名（作者未入力時）を出す", () => {
    render(<ArticleBase article={addonIntroduction} isAuthenticated />);
    const box = within(acquireBox());

    expect(
      box.getByRole("link", { name: "外部配布ページを開く" })
    ).toHaveAttribute("href", "/articles/5/conversion");
    expect(box.getByText("掲載許可")).toBeInTheDocument();
    expect(box.getByText("取得済み")).toBeInTheDocument();
    expect(box.getByText("投稿者")).toBeInTheDocument();
    expect(
      box.queryByRole("link", { name: "利用条件を見る" })
    ).not.toBeInTheDocument();
  });

  it("プレビューでは利用条件へのリンクを動かさず、表示上同じ日時なら更新日を出さない", () => {
    render(
      <ArticleBase
        article={{
          ...addonPost,
          published_at: "2022-11-08T10:00:00",
          modified_at: "2022-11-08T10:00:30",
        }}
        preview
      />
    );

    expect(
      within(acquireBox()).getByRole("link", { name: "利用条件を見る" })
    ).toHaveAttribute("href", "#");
    expect(screen.getByText("2022/11/08 10:00 公開")).toBeInTheDocument();
    expect(screen.queryByText(/更新$/)).not.toBeInTheDocument();
  });

  it("未ログインではマイリスト追加を出さない", () => {
    render(<ArticleBase article={addonPost} />);

    expect(
      screen.queryByRole("button", { name: "マイリストに追加" })
    ).not.toBeInTheDocument();
  });

  it("タグと公開日・更新日はボックスの外（タイトル直下）に出す", () => {
    render(<ArticleBase article={addonPost} />);
    const box = within(acquireBox());

    expect(screen.getByRole("link", { name: "テストタグ" })).toHaveAttribute(
      "href",
      "/tags/21"
    );
    expect(box.queryByRole("link", { name: "テストタグ" })).toBeNull();
    // ライセンスはボックスだけに出す（pak はモックどおり両方に出す）
    expect(screen.getAllByRole("link", { name: "CC BY" })).toHaveLength(1);
    expect(screen.getAllByRole("link", { name: "Pak128.Japan" })).toHaveLength(
      2
    );
    expect(screen.getByText("2026/06/18 12:30 更新")).toBeInTheDocument();
    expect(screen.getByText("2022/11/08 10:00 公開")).toBeInTheDocument();
    expect(box.queryByText(/公開|更新/)).toBeNull();
  });
});
