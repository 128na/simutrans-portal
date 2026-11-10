import { fireEvent, render, screen } from "@testing-library/react";
import { beforeEach, describe, expect, it } from "vitest";
import { CommonForm } from "@/features/articles/forms/CommonForm";
import { createArticle } from "@/features/articles/utils/articleUtil";
import { useArticleEditor } from "@/hooks/useArticleEditor";

const user = {
  id: 1,
  name: "テスト投稿者",
  nickname: null,
  role: "user",
  profile: {
    id: 1,
    data: { avatar: null, description: null, website: [] },
    attachments: [],
  },
} satisfies User.MypageShow;

describe("記事 URL の入力", () => {
  beforeEach(() => {
    const article = createArticle("markdown", user);
    useArticleEditor.setState({ user, article, initialArticle: article });
  });

  it("新規記事ではタイトルから URL を作り、手動修正後は維持する", () => {
    render(<CommonForm />);
    const title = screen.getByRole("textbox", { name: "タイトル" });
    const slug = screen.getByRole("textbox", { name: "記事URL" });

    fireEvent.change(title, { target: { value: "新しい Addon" } });
    expect(slug).toHaveValue("新しい-addon");

    fireEvent.change(slug, { target: { value: "custom-url" } });
    fireEvent.change(title, { target: { value: "後から変更したタイトル" } });
    expect(slug).toHaveValue("custom-url");
  });

  it("既存記事のタイトルを変更しても公開 URL を維持する", () => {
    useArticleEditor.setState((state) => {
      state.article.id = 15;
      state.article.slug = "existing-public-url";
      state.article.status = "publish";
    });
    render(<CommonForm />);

    fireEvent.change(screen.getByRole("textbox", { name: "タイトル" }), {
      target: { value: "新しいタイトル" },
    });
    expect(screen.getByRole("textbox", { name: "記事URL" })).toHaveValue(
      "existing-public-url"
    );
  });
});
