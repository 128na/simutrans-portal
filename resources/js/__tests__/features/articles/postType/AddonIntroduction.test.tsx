import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen, within } from "@testing-library/react";
import { AddonIntroduction } from "@/features/articles/postType/AddonIntroduction";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { useAxiosError } from "@/hooks/useAxiosError";

vi.mock("@/features/articles/components/SelectCategories", () => ({
  SelectCategories: () => <div />,
}));

vi.mock("@/features/tags/TagEdit", () => ({
  TagEdit: () => <div />,
}));

describe("AddonIntroduction", () => {
  beforeEach(() => {
    useAxiosError.getState().clearError();
    useArticleEditor.setState({
      article: {
        id: 1,
        post_type: "addon-introduction",
        contents: { description: "", link: "", author: "", agreement: false },
        categories: [],
        articles: [],
        tags: [],
      } as unknown as Article.MypageEdit,
      tags: [],
      categories: {} as Category.Grouping,
      relationalArticles: [],
    });
  });

  it("「その他の項目」が閉じていても、作者が必須入力として表示される", () => {
    render(<AddonIntroduction />);

    expect(
      screen.getByRole("button", { name: "その他の項目" })
    ).toHaveAttribute("aria-expanded", "false");

    const author = screen.getByText("作者").parentElement as HTMLElement;
    expect(within(author).getByText("必須")).toBeInTheDocument();
    expect(within(author).getByRole("textbox")).toBeRequired();
  });

  it("作者のバリデーションエラーが「その他の項目」が閉じていても表示される", () => {
    useAxiosError.getState().setError({
      errors: { "article.contents.author": ["作者は必ず指定してください。"] },
      message: "作者は必ず指定してください。",
    });

    render(<AddonIntroduction />);

    expect(
      screen.getByRole("button", { name: "その他の項目" })
    ).toHaveAttribute("aria-expanded", "false");
    expect(
      screen.getByText("作者は必ず指定してください。")
    ).toBeInTheDocument();
  });

  it.each([
    ["謝辞", "article.contents.thanks"],
    ["ライセンス", "article.contents.license"],
    ["タグ", "article.tags.0"],
    ["関連記事", "article.articles"],
  ])("「その他の項目」内の%sにエラーがあると自動で開く", (_label, key) => {
    useAxiosError.getState().setError({
      errors: { [key]: ["エラーです。"] },
      message: "エラーです。",
    });

    render(<AddonIntroduction />);

    expect(
      screen.getByRole("button", { name: "その他の項目" })
    ).toHaveAttribute("aria-expanded", "true");
    expect(screen.getByText("エラーです。")).toBeInTheDocument();
  });

  it("関連記事を10件選んでいたら、それ以上選べない", () => {
    useArticleEditor.setState((state) => {
      state.article.articles = Array.from({ length: 10 }, (_, i) => i + 1);
      state.relationalArticles = Array.from({ length: 11 }, (_, i) => ({
        id: i + 1,
        title: `記事${i + 1}`,
        user_name: "u",
      })) as unknown as Article.MypageRelational[];
    });

    render(<AddonIntroduction />);

    expect(
      screen.getByText(/10件まで選べます。追加するには/)
    ).toBeInTheDocument();
    expect(screen.queryByText("記事11 (u)")).not.toBeInTheDocument();
  });
});
