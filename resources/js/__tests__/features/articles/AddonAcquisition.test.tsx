import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { AddonAcquisition } from "@/features/articles/components/AddonAcquisition";

const baseArticle = {
  id: 5,
  title: "テストアドオン",
  slug: "test-addon",
  user: {
    id: 1,
    name: "投稿者",
    nickname: null,
    profile: {
      id: 1,
      data: { avatar: null, description: null, website: [] },
      attachments: [],
    },
  },
  categories: [],
  tags: [],
  articles: [],
  relatedArticles: [],
  published_at: null,
  modified_at: null,
  attachments: [],
};

describe("アドオンの入手欄", () => {
  it("投稿記事のボタンは既存のダウンロード経路を使う", () => {
    render(
      <AddonAcquisition
        preview={false}
        article={{
          ...baseArticle,
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
            description: null,
            file: 3,
            author: null,
            license: null,
            thanks: null,
          },
        }}
      />
    );

    expect(
      screen.getByRole("link", { name: "アドオンをダウンロード" })
    ).toHaveAttribute("href", "/articles/5/download");
    expect(screen.getByText(/addon\.zip/)).toBeInTheDocument();
  });

  it.each([
    ["page", "外部配布ページを開く"],
    ["file", "外部ファイルを開く"],
    [null, "外部の配布先を開く"],
  ] as const)(
    "外部リンクの種別 %s を明示し、計測経路を通す",
    (linkType, label) => {
      const article = {
        ...baseArticle,
        post_type: "addon-introduction",
        addon_page_url: "/articles/5/conversion",
        contents: {
          thumbnail: null,
          description: null,
          link: "https://example.org/addon.zip",
          link_type: linkType,
          author: null,
          license: null,
          thanks: null,
          agreement: true,
          exclude_link_check: false,
        },
      } satisfies Article.Show;
      const { rerender } = render(
        <AddonAcquisition preview={false} article={article} />
      );

      expect(screen.getByRole("link", { name: label })).toHaveAttribute(
        "href",
        "/articles/5/conversion"
      );
      rerender(<AddonAcquisition preview article={article} />);
      expect(
        screen.queryByRole("link", { name: label })
      ).not.toBeInTheDocument();
      expect(screen.getByRole("button", { name: label })).toBeDisabled();
    }
  );
});
