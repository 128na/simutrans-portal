import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import userEvent from "@testing-library/user-event";
import axios from "axios";
import { ArticleDeleteModal } from "@/features/articles/ArticleDeleteModal";
import { ToastProvider } from "@/providers/ToastProvider";

vi.mock("axios");
const mockAxios = axios;

describe("ArticleDeleteModal コンポーネント", () => {
  const mockArticle: Article.MypageShow = {
    id: 1,
    user_id: 1,
    title: "テスト記事",
    slug: "test-article",
    post_type: "markdown",
    status: "draft",
    attachments: [],
    total_conversion_count: null,
    total_view_count: null,
    published_at: null,
    modified_at: "2025-01-01 10:00:00",
  };

  beforeEach(() => {
    vi.clearAllMocks();
    mockAxios.delete = vi.fn().mockResolvedValue({ data: {} });
  });

  it("記事のタイトルを含む確認メッセージが表示される", () => {
    render(
      <ToastProvider>
        <ArticleDeleteModal
          article={mockArticle}
          onClose={vi.fn()}
          onSuccess={vi.fn()}
        />
      </ToastProvider>
    );

    expect(screen.getByText(/テスト記事/)).toBeInTheDocument();
  });

  it("削除ボタンクリックで削除APIが呼ばれ成功時にonSuccessが呼ばれる", async () => {
    const user = userEvent.setup();
    const onSuccess = vi.fn();

    render(
      <ToastProvider>
        <ArticleDeleteModal
          article={mockArticle}
          onClose={vi.fn()}
          onSuccess={onSuccess}
        />
      </ToastProvider>
    );

    await user.click(screen.getByRole("button", { name: "削除" }));

    await waitFor(() => {
      expect(mockAxios.delete).toHaveBeenCalledWith("/api/v2/articles/1");
      expect(onSuccess).toHaveBeenCalled();
    });
  });

  it("キャンセルボタンクリックでonCloseが呼ばれる", async () => {
    const user = userEvent.setup();
    const onClose = vi.fn();

    render(
      <ToastProvider>
        <ArticleDeleteModal
          article={mockArticle}
          onClose={onClose}
          onSuccess={vi.fn()}
        />
      </ToastProvider>
    );

    await user.click(screen.getByRole("button", { name: "キャンセル" }));

    expect(onClose).toHaveBeenCalled();
  });

  it("articleがnullの場合は何も表示されない", () => {
    const { container } = render(
      <ToastProvider>
        <ArticleDeleteModal
          article={null}
          onClose={vi.fn()}
          onSuccess={vi.fn()}
        />
      </ToastProvider>
    );

    expect(container).toBeEmptyDOMElement();
  });
});
