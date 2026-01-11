import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { ArticleForm } from "@/features/articles/ArticleForm";
import { useArticleEditor } from "@/hooks/useArticleEditor";

// モック
vi.mock("@/hooks/useArticleEditor", () => ({
  useArticleEditor: vi.fn(),
}));

vi.mock("@/hooks/useAxiosError", () => ({
  useAxiosError: () => ({
    getError: vi.fn(() => null),
  }),
}));

vi.mock("@/features/articles/forms/CommonForm", () => ({
  CommonForm: () => <div data-testid="common-form">CommonForm</div>,
}));

vi.mock("@/features/articles/forms/StatusForm", () => ({
  StatusForm: () => <div data-testid="status-form">StatusForm</div>,
}));

vi.mock("@/features/articles/postType/Page", () => ({
  Page: () => <div data-testid="post-type-page">Page</div>,
}));

vi.mock("@/features/articles/postType/Markdown", () => ({
  Markdown: () => <div data-testid="post-type-markdown">Markdown</div>,
}));

vi.mock("@/features/articles/postType/AddonPost", () => ({
  AddonPost: () => <div data-testid="post-type-addon-post">AddonPost</div>,
}));

vi.mock("@/features/articles/postType/AddonIntroduction", () => ({
  AddonIntroduction: () => (
    <div data-testid="post-type-addon-introduction">AddonIntroduction</div>
  ),
}));

describe("ArticleForm", () => {
  const mockSetState = vi.fn();
  // @ts-expect-error - Mock for testing
  const mockUseArticleEditor = useArticleEditor;

  // Zustand の setState メソッドをモック
  beforeEach(() => {
    vi.clearAllMocks();
    mockUseArticleEditor.setState = mockSetState;
  });

  const createMockArticle = (
    postType: ArticlePostType,
    status: ArticleStatus = "draft"
  ) => ({
    id: 1,
    title: "Test Article",
    slug: "test-article",
    post_type: postType,
    status,
    contents: { thumbnail: null },
    categories: [],
    articles: [],
    tags: [],
  });

  it("記事がない場合は何も表示しない", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: null,
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    const { container } = render(<ArticleForm />);
    expect(container.firstChild).toBeNull();
  });

  it("post_typeがない場合は何も表示しない", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: { id: 1, title: "Test", post_type: null },
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    const { container } = render(<ArticleForm />);
    expect(container.firstChild).toBeNull();
  });

  it("post_typeがpageの場合、Pageコンポーネントを表示", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(screen.getByTestId("post-type-page")).toBeInTheDocument();
    expect(screen.queryByTestId("post-type-markdown")).not.toBeInTheDocument();
  });

  it("post_typeがmarkdownの場合、Markdownコンポーネントを表示", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("markdown"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(screen.getByTestId("post-type-markdown")).toBeInTheDocument();
    expect(screen.queryByTestId("post-type-page")).not.toBeInTheDocument();
  });

  it("post_typeがaddon-postの場合、AddonPostコンポーネントを表示", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("addon-post"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(screen.getByTestId("post-type-addon-post")).toBeInTheDocument();
  });

  it("post_typeがaddon-introductionの場合、AddonIntroductionコンポーネントを表示", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("addon-introduction"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(
      screen.getByTestId("post-type-addon-introduction")
    ).toBeInTheDocument();
  });

  it("CommonFormとStatusFormが常に表示される", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(screen.getByTestId("common-form")).toBeInTheDocument();
    expect(screen.getByTestId("status-form")).toBeInTheDocument();
  });

  it("更新日時のチェックボックスが正しく機能する", async () => {
    const user = userEvent.setup();
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    const checkbox = screen.getByRole("checkbox", { name: /更新しない/ });

    await user.click(checkbox);

    await waitFor(() => {
      expect(mockSetState).toHaveBeenCalled();
    });
  });

  it("記事が公開状態の場合、SNS通知のチェックボックスが表示される", () => {
    const mockUpdateShouldNotify = vi.fn();
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page", "publish"),
        shouldNotify: false,
        updateShouldNotify: mockUpdateShouldNotify,
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(screen.getByText("公開時のSNS通知")).toBeInTheDocument();
    expect(
      screen.getByRole("checkbox", { name: /通知する/ })
    ).toBeInTheDocument();
  });

  it("記事が下書き状態の場合、SNS通知のチェックボックスが表示されない", () => {
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page", "draft"),
        shouldNotify: false,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    expect(screen.queryByText("公開時のSNS通知")).not.toBeInTheDocument();
  });

  it("SNS通知のチェックボックスをクリックすると状態が更新される", async () => {
    const user = userEvent.setup();
    const mockUpdateShouldNotify = vi.fn();
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page", "publish"),
        shouldNotify: false,
        updateShouldNotify: mockUpdateShouldNotify,
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    const checkbox = screen.getByRole("checkbox", { name: /通知する/ });

    await user.click(checkbox);

    await waitFor(() => {
      expect(mockUpdateShouldNotify).toHaveBeenCalledWith(true);
    });
  });

  it("更新日時を変えないときSNS通知もOFFになる", async () => {
    const user = userEvent.setup();
    mockUseArticleEditor.mockImplementation((selector) => {
      const state = {
        article: createMockArticle("page", "publish"),
        shouldNotify: true,
        updateShouldNotify: vi.fn(),
        withoutUpdateModifiedAt: false,
      };
      return selector(state);
    });

    render(<ArticleForm />);
    const checkbox = screen.getByRole("checkbox", { name: /更新しない/ });

    await user.click(checkbox);

    await waitFor(() => {
      expect(mockSetState).toHaveBeenCalledWith(expect.any(Function));
      const stateUpdater = mockSetState.mock.calls[0][0];
      const mockState = {
        shouldNotify: true,
        withoutUpdateModifiedAt: false,
      };
      stateUpdater(mockState);
      expect(mockState.shouldNotify).toBe(false);
      expect(mockState.withoutUpdateModifiedAt).toBe(true);
    });
  });
});
