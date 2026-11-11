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
    window.location = { replace: vi.fn() } as unknown as Location;
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

  describe("公開と下書き保存の操作", () => {
    const mockEditor = (
      article: Article.MypageEdit,
      initialStatus: ArticleStatus | undefined,
      shouldNotify = false
    ) => {
      vi.mocked(useArticleEditor).mockImplementation((selector) =>
        selector({
          article,
          initialArticle: initialStatus
            ? { ...article, status: initialStatus }
            : undefined,
          shouldNotify,
          withoutUpdateModifiedAt: false,
          followRedirect: false,
        } as unknown as ReturnType<typeof useArticleEditor.getState>)
      );
    };

    const lastPost = () => vi.mocked(mockedAxios.post).mock.calls.at(-1);

    beforeEach(() => {
      vi.spyOn(mockedAxios, "post").mockResolvedValue({
        data: { article_id: 41 },
      } as AxiosResponse);
    });

    it.each([
      ["新規", null, undefined, "/api/v2/articles"],
      ["編集中の下書き", 7, "draft", "/api/v2/articles/7"],
    ] as const)(
      "%sで「公開する」を押すと、ステータス欄で公開を選んで保存したときと同じ値を送る",
      async (_, id, initialStatus, url) => {
        const draft = {
          ...mockArticle,
          id,
          status: "draft",
          published_at: null,
        } as Article.MypageEdit;

        mockEditor({ ...draft, status: "publish" }, initialStatus, true);
        const { unmount } = render(<ArticleEdit />);
        fireEvent.click(screen.getByRole("button", { name: "公開する" }));
        await waitFor(() => expect(mockedAxios.post).toHaveBeenCalledTimes(1));
        const viaStatusField = lastPost();
        unmount();

        mockEditor(draft, initialStatus, true);
        render(<ArticleEdit />);
        fireEvent.click(screen.getByRole("button", { name: "公開する" }));
        await waitFor(() => expect(mockedAxios.post).toHaveBeenCalledTimes(2));
        const viaPublishButton = lastPost();

        expect(viaPublishButton).toEqual(viaStatusField);
        expect(viaPublishButton?.[0]).toBe(url);
        expect(viaPublishButton?.[1]).toMatchObject({
          article: { status: "publish", published_at: null },
          should_notify: true,
        });
        expect(window.location.replace).toHaveBeenLastCalledWith(
          "/mypage/articles/edit/41?updated=1&saved=publish"
        );
      }
    );

    it("「下書きを保存」は下書きのまま保存する", async () => {
      mockEditor({ ...mockArticle, id: 7, status: "publish" }, "draft");
      render(<ArticleEdit />);
      fireEvent.click(screen.getByRole("button", { name: "下書きを保存" }));

      await waitFor(() =>
        expect(mockedAxios.post).toHaveBeenCalledWith(
          "/api/v2/articles/7",
          expect.objectContaining({
            article: expect.objectContaining({ id: 7, status: "draft" }),
          })
        )
      );
      expect(window.location.replace).toHaveBeenCalledWith(
        "/mypage/articles/edit/41?updated=1&saved=draft"
      );
    });

    it.each([
      ["reservation", "公開を予約する", "2030-01-02T03:04"],
      ["private", "非公開で保存", "2025-01-01 10:00:00"],
    ] as const)(
      "%s の記事は公開操作を出さず、状態と公開日時を保ったまま保存する",
      async (status, label, publishedAt) => {
        mockEditor(
          { ...mockArticle, id: 8, status, published_at: publishedAt },
          status
        );
        render(<ArticleEdit />);

        expect(
          screen.queryByRole("button", { name: "公開する" })
        ).not.toBeInTheDocument();
        expect(
          screen.queryByRole("button", { name: "下書きを保存" })
        ).not.toBeInTheDocument();
        fireEvent.click(screen.getByRole("button", { name: label }));

        await waitFor(() =>
          expect(mockedAxios.post).toHaveBeenCalledWith(
            "/api/v2/articles/8",
            expect.objectContaining({
              article: expect.objectContaining({
                status,
                published_at: publishedAt,
              }),
            })
          )
        );
        expect(window.location.replace).toHaveBeenCalledWith(
          `/mypage/articles/edit/41?updated=1&saved=${status}`
        );
      }
    );

    it("公開中の記事は「公開内容を更新」だけを出し、下書きに戻す操作を出さない", () => {
      mockEditor({ ...mockArticle, id: 9, status: "publish" }, "publish");
      render(<ArticleEdit />);

      expect(
        screen.getByRole("button", { name: "公開内容を更新" })
      ).toBeInTheDocument();
      expect(
        screen.queryByRole("button", { name: "公開する" })
      ).not.toBeInTheDocument();
      expect(
        screen.queryByRole("button", { name: "下書きを保存" })
      ).not.toBeInTheDocument();
    });

    it("公開中の記事をステータス欄で下書きにしたときは、公開操作を出さず下書きで保存する", async () => {
      mockEditor({ ...mockArticle, id: 12, status: "draft" }, "publish");
      render(<ArticleEdit />);

      expect(
        screen.queryByRole("button", { name: "公開する" })
      ).not.toBeInTheDocument();
      fireEvent.click(screen.getByRole("button", { name: "下書きを保存" }));

      await waitFor(() =>
        expect(mockedAxios.post).toHaveBeenCalledWith(
          "/api/v2/articles/12",
          expect.objectContaining({
            article: expect.objectContaining({ id: 12, status: "draft" }),
          })
        )
      );
    });
  });

  it("プレビューを切り替えても編集欄を破棄しない", () => {
    render(<ArticleEdit />);
    const form = screen.getByTestId("article-form");
    const toggle = screen.getByRole("switch", { name: "プレビュー表示" });
    expect(toggle).not.toBeChecked();
    const editor = document.getElementById("article-edit-panel");
    const preview = document.getElementById("article-preview-panel");
    expect(editor).not.toHaveClass("hidden");
    expect(preview).toHaveClass("hidden");
    fireEvent.click(toggle);
    expect(toggle).toBeChecked();
    expect(editor).toHaveClass("hidden");
    expect(preview).not.toHaveClass("hidden");
    expect(screen.getByTestId("article-form")).toBe(form);
    fireEvent.click(toggle);
    expect(toggle).not.toBeChecked();
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

  it("添付のアップロードが終わるまで保存を無効にする", () => {
    vi.mocked(useArticleEditor).mockImplementation((selector) =>
      selector({
        article: mockArticle,
        initialArticle: mockArticle,
        uploadsInProgress: 1,
      } as ReturnType<typeof useArticleEditor.getState>)
    );
    render(<ArticleEdit />);
    expect(
      screen.getByRole("button", { name: "アップロード中…" })
    ).toBeDisabled();
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
      expect(window.location.replace).toHaveBeenCalledWith(
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
