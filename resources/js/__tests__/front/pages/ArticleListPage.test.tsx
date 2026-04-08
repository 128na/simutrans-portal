import React from "react";
import { describe, expect, it, beforeEach, vi } from "vitest";
import { act, waitFor } from "@testing-library/react";

let renderedAuthenticated = false;

vi.mock("../../../features/articles/components/ArticleList", () => ({
  ArticleList: ({
    articles,
    isAuthenticated,
  }: {
    articles: Array<{ title: string }>;
    isAuthenticated: boolean;
  }) => {
    renderedAuthenticated = isAuthenticated;
    return (
      <ul data-testid="articles">
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

describe("ArticleListPage", () => {
  beforeEach(() => {
    renderedAuthenticated = false;
    vi.resetModules();
  });
  it("renders articles from embedded JSON", async () => {
    document.body.innerHTML = `
      <div id="app-article-list"></div>
      <script id="data-articles" type="application/json">[{"title":"a1"},{"title":"a2"}]</script>
      <script id="data-is-authenticated" type="application/json">
        true
      </script>
    `;

    await act(async () => {
      await import("../../../front/pages/ArticleListPage");
    });

    await waitFor(() => {
      const list = document.querySelector('[data-testid="articles"]');
      expect(list).not.toBeNull();
      expect(list!.textContent).toContain("a1");
      expect(list!.textContent).toContain("a2");
    });
  });

  it("parses authenticated state even when whitespace is present", async () => {
    document.body.innerHTML = `
      <div id="app-article-list"></div>
      <script id="data-articles" type="application/json">[{"title":"a1"}]</script>
      <script id="data-is-authenticated" type="application/json">
        true
      </script>
    `;

    await act(async () => {
      await import("../../../front/pages/ArticleListPage");
    });

    await waitFor(() => {
      expect(renderedAuthenticated).toBe(true);
    });
  });
});
