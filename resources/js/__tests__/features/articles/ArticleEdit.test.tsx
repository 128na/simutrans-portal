import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import { ArticleEdit } from "@/features/articles/ArticleEdit";
import {
  useArticleEditor,
  useIsArticleUpdated,
} from "@/hooks/useArticleEditor";
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
const mockSetValidationErrorFrom = vi.fn();
vi.mock("@/hooks/useAxiosError", () => ({
  useAxiosError: () => ({
    setValidationErrorFrom: mockSetValidationErrorFrom,
    getError: vi.fn(),
    hasError: vi.fn(),
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
    contents: { sections: [], thumbnail: null },
    published_at: "2025-01-01 10:00:00",
    modified_at: "2025-01-02 12:00:00",
    categories: [],
    tags: [],
    articles: [],
    attachments: [],
  };

  beforeEach(() => {
    vi.clearAllMocks();
    vi.mocked(useIsArticleUpdated).mockReturnValue(false);

    // useArticleEditor のデフォルトモック
    (
      useArticleEditor as unknown as ReturnType<typeof vi.fn>
    ).mockImplementation((selector: (state: unknown) => unknown) => {
      const mockState = {
        article: mockArticle,
        initialArticle: mockArticle,
        shouldNotify: false,
        withoutUpdateModifiedAt: false,
        followRedirect: false,
      };
      return selector(mockState);
    });

    // window.location.href のモック
    delete (window as { location?: unknown }).location;
    (window as { location: { href: string } }).location = { href: "" };
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

    expect(
      screen.getByRole("button", { name: /公開内容を更新|公開する/ })
    ).toBeInTheDocument();
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

    vi.spyOn(mockedAxios, "post").mockResolvedValue({
      data: { article_id: 1 },
    } as AxiosResponse);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", {
      name: /公開内容を更新|公開する/,
    });
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
    vi.spyOn(mockedAxios, "post").mockResolvedValue({
      data: { article_id: 1 },
    } as AxiosResponse);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", {
      name: /公開内容を更新|公開する/,
    });
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

  it.each([
    ["draft", "下書きを保存"],
    ["reservation", "公開を予約する"],
    ["publish", "公開する"],
  ] as const)(
    "新規記事の状態 %s に応じて保存操作を表示する",
    (status, label) => {
      vi.mocked(useArticleEditor).mockImplementation((selector) =>
        selector({
          article: { ...mockArticle, id: null, status },
        } as ReturnType<typeof useArticleEditor.getState>)
      );
      render(<ArticleEdit />);
      expect(screen.getByRole("button", { name: label })).toBeInTheDocument();
    }
  );

  it("プレビューを切り替えても編集欄を破棄しない", () => {
    render(<ArticleEdit />);
    const form = screen.getByTestId("article-form");
    fireEvent.click(screen.getByRole("button", { name: "プレビュー" }));
    expect(screen.getByRole("button", { name: "プレビュー" })).toHaveAttribute(
      "aria-pressed",
      "true"
    );
    expect(screen.getByTestId("article-form")).toBe(form);
    fireEvent.click(screen.getByRole("button", { name: "編集" }));
    expect(screen.getByRole("button", { name: "編集" })).toHaveAttribute(
      "aria-pressed",
      "true"
    );
  });

  it("未保存のときだけ離脱を警告する", () => {
    const { rerender } = render(<ArticleEdit />);
    expect(
      window.dispatchEvent(new Event("beforeunload", { cancelable: true }))
    ).toBe(true);

    vi.mocked(useIsArticleUpdated).mockReturnValue(true);
    rerender(<ArticleEdit />);
    expect(
      window.dispatchEvent(new Event("beforeunload", { cancelable: true }))
    ).toBe(false);
  });

  it("保存成功後にリダイレクトする", async () => {
    vi.spyOn(mockedAxios, "post").mockResolvedValue({
      data: { article_id: 123 },
    } as AxiosResponse);

    render(<ArticleEdit />);
    const saveButton = screen.getByRole("button", {
      name: /公開内容を更新|公開する/,
    });
    fireEvent.click(saveButton);

    await waitFor(() => {
      expect(window.location.href).toBe(
        "/mypage/articles/edit/123?updated=1&saved=update"
      );
    });
  });

  it("バリデーションエラー発生時にsetValidationErrorFromが呼ばれる", async () => {
    // scrollTo のモック
    Element.prototype.scrollTo = vi.fn();
    mockSetValidationErrorFrom.mockReturnValue(true);

    const validationError = {
      isAxiosError: true,
      response: {
        status: 422,
        data: {
          errors: { "article.title": ["タイトルは必須です"] },
          message: null,
        },
      },
    };

    vi.spyOn(mockedAxios, "post").mockRejectedValue(validationError);

    render(<ArticleEdit />);
    fireEvent.click(
      screen.getByRole("button", { name: /公開内容を更新|公開する/ })
    );

    await waitFor(() => {
      expect(mockSetValidationErrorFrom).toHaveBeenCalledWith(validationError);
    });
  });

  it("保存失敗を表示し、未保存の変更を保持する", async () => {
    vi.mocked(useIsArticleUpdated).mockReturnValue(true);
    mockSetValidationErrorFrom.mockReturnValue(false);

    const generalError = new Error("Network error");
    vi.spyOn(mockedAxios, "post").mockRejectedValue(generalError);

    render(<ArticleEdit />);
    fireEvent.click(
      screen.getByRole("button", { name: /公開内容を更新|公開する/ })
    );

    await waitFor(() => {
      expect(screen.getByRole("alert")).toHaveTextContent(
        "入力内容を保持しています"
      );
      expect(screen.getByText(/未保存の変更/)).toBeInTheDocument();
    });
  });
});
