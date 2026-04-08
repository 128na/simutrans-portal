import React from "react";
import { describe, expect, it, beforeEach, vi } from "vitest";
import { act, waitFor } from "@testing-library/react";

let renderedAuthenticated = false;

vi.mock("../../../features/articles/components/ArticleBase", () => ({
  ArticleBase: ({
    article,
    isAuthenticated,
  }: {
    article: { title: string };
    isAuthenticated: boolean;
  }) => {
    renderedAuthenticated = isAuthenticated;
    return <div data-testid="article-title">{article.title}</div>;
  },
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("ArticleShowPage", () => {
  beforeEach(() => {
    renderedAuthenticated = false;
    vi.resetModules();
  });
  it("renders article data from embedded JSON", async () => {
    document.body.innerHTML = `
      <div id="app-article-show"></div>
      <script id="data-article" type="application/json">{"title":"hello"}</script>
      <script id="data-is-authenticated" type="application/json">
        true
      </script>
    `;

    await act(async () => {
      await import("../../../front/pages/ArticleShowPage");
    });

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="article-title"]')?.textContent
      ).toBe("hello");
    });
  });

  it("parses authenticated state with whitespace preserved", async () => {
    document.body.innerHTML = `
      <div id="app-article-show"></div>
      <script id="data-article" type="application/json">{"title":"hello"}</script>
      <script id="data-is-authenticated" type="application/json">
        true
      </script>
    `;

    await act(async () => {
      await import("../../../front/pages/ArticleShowPage");
    });

    await waitFor(() => {
      expect(renderedAuthenticated).toBe(true);
    });
  });
});
