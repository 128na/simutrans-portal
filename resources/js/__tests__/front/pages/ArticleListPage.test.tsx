import { describe, expect, it } from "vitest";
import { waitFor } from "@testing-library/react";

vi.mock("../../../features/articles/components/ArticleList", () => ({
  ArticleList: ({ articles }: { articles: Array<{ title: string }> }) => (
    <ul data-testid="articles">
      {articles.map((a) => (
        <li key={a.title}>{a.title}</li>
      ))}
    </ul>
  ),
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("ArticleListPage", () => {
  it("renders articles from embedded JSON", async () => {
    document.body.innerHTML = `
      <div id="app-article-list"></div>
      <script id="data-articles" type="application/json">[{"title":"a1"},{"title":"a2"}]</script>
    `;

    await import("../../../front/pages/ArticleListPage");

    await waitFor(() => {
      const list = document.querySelector('[data-testid="articles"]');
      expect(list).not.toBeNull();
      expect(list!.textContent).toContain("a1");
      expect(list!.textContent).toContain("a2");
    });
  });
});
