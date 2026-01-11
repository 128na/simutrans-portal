import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import { ArticleEdit } from "@/features/articles/ArticleEdit";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import axios from "axios";
import type { AxiosResponse } from "axios";

// Zustand ストアをモック
vi.mock("@/hooks/useArticleEditor");

// ArticleForm と ArticlePreview をモック
vi.mock("@/features/articles/ArticleForm", () => ({
  ArticleForm: () => <div data-testid="article-form">Article Form</div>,
}));

vi.mock("@/features/articles/ArticlePreview", () => ({
  ArticlePreview: () => (
    <div data-testid="article-preview">Article Preview</div>
  ),
}));

// axios をモック
vi.mock("axios");
const mockedAxios = vi.mocked(axios);

// useAxiosError をモック
const mockSetError = vi.fn();
vi.mock("@/hooks/useAxiosError", () => ({
  useAxiosError: () => ({
    setError: mockSetError,
    getError: vi.fn(),
    hasError: vi.fn(),
  }),
}));

// useErrorHandler をモック
const mockHandleErrorWithContext = vi.fn();
vi.mock("@/hooks/useErrorHandler", () => ({
  useErrorHandler: () => ({
    handleErrorWithContext: mockHandleErrorWithContext,
  }),
}));

// Button をモック
vi.mock("@/components/ui/Button", () => ({
  default: ({
    children,
    onClick,
    ...props
  }: React.ButtonHTMLAttributes<HTMLButtonElement>) => (
    <button onClick={onClick} {...props}>
      {children}
    </button>
  ),
}));

describe("ArticleEdit", () => {
  const mockArticle: Article.MypageEdit = {
    id: 1,
    user_id: 1,
    slug: "test-article",
    title: "Test Article",
    status: "publish",
    post_type: "page",
    contents: {},
    published_at: "2025-01-01 10:00:00",
    modified_at: "2025-01-02 12:00:00",
  };

  beforeEach(() => {
    vi.clearAllMocks();

    // useArticleEditor のデフォルトモック
    (
      useArticleEditor as unknown as ReturnType<typeof vi.fn>
    ).mockImplementation((selector: (state: unknown) => unknown) => {
      const mockState = {
        article: mockArticle,
        shouldNotify: false,
        withoutUpdateModifiedAt: false,
        followRedirect: false,
      };
      return selector(mockState);
    });

    // window.location.href のモック
    delete (window as { location?: unknown }).location;
    window.location = { href: "" } as Location;
  });

  it("記事のpost_typeが存在しない場合は何も表示しない", () => {
    (
      useArticleEditor as unknown as ReturnType<typeof vi.fn>
    ).mockImplementation((selector: (state: unknown) => unknown) => {
      const mockState = {
        article: { ...mockArticle, post_type: null },
        shouldNotify: false,
        withoutUpdateModifiedAt: false,
        followRedirect: false,
      };
      return selector(mockState);
    });

    const { container } = render(<ArticleEdit />);
    expect(container.firstChild).toBeNull();
  });

  it("ArticleForm と ArticlePreview が表示される", () => {
    render(<ArticleEdit />);

    expect(screen.getByTestId("article-form")).toBeInTheDocument();
    expect(screen.getByTestId("article-preview")).toBeInTheDocument();
  });

  it("保存ボタンが表示される", () => {
    render(<ArticleEdit />);

    expect(screen.getByRole("button", { name: "保存" })).toBeInTheDocument();
  });

  it("保存ボタンクリックで新規作成APIを呼ぶ（id なし）", async () => {
    (
      useArticleEditor as unknown as ReturnType<typeof vi.fn>
    ).mockImplementation((selector: (state: unknown) => unknown) => {
      const mockState = {
        article: { ...mockArticle, id: undefined },
        shouldNotify: true,
        withoutUpdateModifiedAt: false,
        followRedirect: false,
      };
      return selector(mockState);
    });

    mockedAxios.post.mockResolvedValue({
      data: { article_id: 1 },
    } as AxiosResponse);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", { name: "保存" });
    fireEvent.click(saveButton);

    await waitFor(() => {
      expect(mockedAxios.post).toHaveBeenCalledWith("/api/v2/articles", {
        article: expect.objectContaining({ slug: "test-article" }),
        should_notify: true,
        without_update_modified_at: false,
        follow_redirect: false,
      });
    });
  });

  it("保存ボタンクリックで更新APIを呼ぶ（id あり）", async () => {
    mockedAxios.post.mockResolvedValue({
      data: { article_id: 1 },
    } as AxiosResponse);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", { name: "保存" });
    fireEvent.click(saveButton);

    await waitFor(() => {
      expect(mockedAxios.post).toHaveBeenCalledWith("/api/v2/articles/1", {
        article: expect.objectContaining({ id: 1 }),
        should_notify: false,
        without_update_modified_at: false,
        follow_redirect: false,
      });
    });
  });

  it("保存成功後にリダイレクトする", async () => {
    mockedAxios.post.mockResolvedValue({
      data: { article_id: 123 },
    } as AxiosResponse);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", { name: "保存" });
    fireEvent.click(saveButton);

    await waitFor(() => {
      expect(window.location.href).toBe("/mypage/articles/edit/123?updated=1");
    });
  });

  it("バリデーションエラー発生時にエラーを設定する", async () => {
    // scrollTo のモック
    Element.prototype.scrollTo = vi.fn();

    const validationError = {
      isAxiosError: true,
      response: {
        status: 422,
        data: {
          errors: {
            "article.title": ["タイトルは必須です"],
          },
        },
      },
    };

    mockedAxios.post.mockRejectedValue(validationError);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", { name: "保存" });
    fireEvent.click(saveButton);

    await waitFor(() => {
      expect(mockedAxios.post).toHaveBeenCalled();
      expect(mockSetError).toHaveBeenCalledWith(validationError.response.data);
    });
  });

  it("一般的なエラー発生時にエラーハンドラーが呼ばれる", async () => {
    const generalError = new Error("Network error");
    mockedAxios.post.mockRejectedValue(generalError);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", { name: "保存" });
    fireEvent.click(saveButton);

    await waitFor(() => {
      expect(mockedAxios.post).toHaveBeenCalled();
      expect(mockHandleErrorWithContext).toHaveBeenCalledWith(generalError, {
        action: "save",
      });
    });
  });
});
