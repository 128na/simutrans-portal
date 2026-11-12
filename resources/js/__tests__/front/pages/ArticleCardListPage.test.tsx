import React from "react";
import { describe, expect, it, beforeEach, vi } from "vitest";
import { act, waitFor } from "@testing-library/react";

let rendered: { sort?: string; isAuthenticated?: boolean } = {};

vi.mock("../../../features/articles/components/ArticleCardList", () => ({
  ArticleCardList: ({
    articles,
    sort,
    isAuthenticated,
  }: {
    articles: Array<{ title: string }>;
    sort: string;
    isAuthenticated: boolean;
  }) => {
    rendered = { sort, isAuthenticated };
    return (
      <ul data-testid="cards">
        {articles.map((a) => (
          <li key={a.title}>{a.title}</li>
        ))}
      </ul>
    );
  },
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("ArticleCardListPage", () => {
  beforeEach(() => {
    rendered = {};
    vi.resetModules();
  });

  it("埋め込みの記事・並び順・ログイン状態でカード一覧を描画する", async () => {
    document.body.innerHTML = `
      <div id="app-article-cards" data-sort="published"></div>
      <script id="data-articles" type="application/json">[{"title":"c1"},{"title":"c2"}]</script>
      <script id="data-is-authenticated" type="application/json"> true </script>
    `;

    await act(async () => {
      await import("../../../front/pages/ArticleCardListPage");
    });

    await waitFor(() => {
      const list = document.querySelector('[data-testid="cards"]');
      expect(list?.textContent).toBe("c1c2");
    });
    expect(rendered).toEqual({ sort: "published", isAuthenticated: true });
  });

  it.each(["unknown", "popular"])(
    "並び順が %s ならカードに更新日を出す（更新順として扱う）",
    async (sort) => {
      document.body.innerHTML = `
      <div id="app-article-cards" data-sort="${sort}"></div>
      <script id="data-articles" type="application/json">[]</script>
    `;

      await act(async () => {
        await import("../../../front/pages/ArticleCardListPage");
      });

      await waitFor(() => {
        expect(rendered).toEqual({ sort: "modified", isAuthenticated: false });
      });
    }
  );
});
