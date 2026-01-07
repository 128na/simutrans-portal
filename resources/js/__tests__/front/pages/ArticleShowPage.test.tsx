import React from "react";
import { describe, expect, it } from "vitest";
import { waitFor } from "@testing-library/react";

vi.mock("../../../features/articles/components/ArticleBase", () => ({
  ArticleBase: ({ article }: { article: { title: string } }) => (
    <div data-testid="article-title">{article.title}</div>
  ),
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("ArticleShowPage", () => {
  it("renders article data from embedded JSON", async () => {
    document.body.innerHTML = `
      <div id="app-article-show"></div>
      <script id="data-article" type="application/json">{"title":"hello"}</script>
    `;

    await import("../../../front/pages/ArticleShowPage");

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="article-title"]')?.textContent
      ).toBe("hello");
    });
  });
});
