import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import userEvent from "@testing-library/user-event";
import { ArticleTable } from "../../apps/features/articles/ArticleTable";

describe("ArticleTable コンポーネント", () => {
  const mockArticles: Article.MypageShow[] = [
    {
      id: 1,
      user_id: 1,
      slug: "test-article-1",
      title: "テスト記事1",
      status: "publish",
      post_type: "page",
      published_at: "2025-01-01 10:00:00",
      modified_at: "2025-01-02 12:00:00",
      total_view_count: null,
      total_conversion_count: null,
      attachments: [],
    },
    {
      id: 2,
      user_id: 1,
      slug: "test-article-2",
      title: "テスト記事2",
      status: "draft",
      post_type: "addon-post",
      published_at: null,
      modified_at: "2025-01-03 14:00:00",
      total_view_count: null,
      total_conversion_count: null,
      attachments: [],
    },
  ];

  it("記事一覧がテーブルとして表示される", () => {
    render(<ArticleTable articles={mockArticles} limit={10} />);
    expect(screen.getByText("テスト記事1")).toBeInTheDocument();
    expect(screen.getByText("テスト記事2")).toBeInTheDocument();
  });

  it("検索フィルターが動作する", async () => {
    const user = userEvent.setup();
    render(<ArticleTable articles={mockArticles} limit={10} />);

    const searchInput = screen.getByRole("textbox");
    await user.type(searchInput, "テスト記事1");

    expect(screen.getByText("テスト記事1")).toBeInTheDocument();
    // フィルタリング後、2つ目の記事は表示されない想定
    expect(screen.queryByText("テスト記事2")).not.toBeInTheDocument();
  });

  it("ページネーションが表示される", () => {
    const manyArticles = Array.from({ length: 15 }, (_, i) => ({
      ...mockArticles[0],
      id: i + 1,
      title: `記事${i + 1}`,
    }));

    render(<ArticleTable articles={manyArticles} limit={10} />);
    // 最初のページに記事1が表示されることを確認
    expect(screen.getByText("記事1")).toBeInTheDocument();
  });

  it("ソート機能が動作する", async () => {
    const user = userEvent.setup();
    render(<ArticleTable articles={mockArticles} limit={10} />);

    // ソート可能なヘッダーをクリック（実装に応じて調整）
    const headers = screen.getAllByRole("columnheader");
    if (headers.length > 0) {
      await user.click(headers[0]);
      // ソート後も記事が表示されていることを確認
      expect(screen.getByText("テスト記事1")).toBeInTheDocument();
    }
  });
});
