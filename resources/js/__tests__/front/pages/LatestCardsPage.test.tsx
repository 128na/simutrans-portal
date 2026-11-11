import React from "react";
import { describe, expect, it, beforeEach, vi } from "vitest";
import { act, waitFor } from "@testing-library/react";

let rendered: Array<{ titles: string[]; isAuthenticated?: boolean }> = [];

vi.mock("../../../features/articles/components/LatestArticleCardList", () => ({
  LatestArticleCardList: ({
    articles,
    isAuthenticated,
  }: {
    articles: Array<{ title: string }>;
    isAuthenticated: boolean;
  }) => {
    rendered.push({ titles: articles.map((a) => a.title), isAuthenticated });
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

describe("LatestCardsPage", () => {
  beforeEach(() => {
    rendered = [];
    vi.resetModules();
  });

  it("/latest のように新着が複数あれば、それぞれ指定された埋め込みデータで描画する", async () => {
    document.body.innerHTML = `
      <script id="data-latest-cards-pak128" type="application/json">[{"title":"a1"},{"title":"a2"}]</script>
      <div id="first" data-latest-cards="data-latest-cards-pak128">読み込み中...</div>
      <script id="data-latest-cards-pak64" type="application/json">[{"title":"b1"}]</script>
      <div id="second" data-latest-cards="data-latest-cards-pak64">読み込み中...</div>
      <script id="data-is-authenticated" type="application/json"> true </script>
    `;

    await act(async () => {
      await import("../../../front/pages/LatestCardsPage");
    });

    await waitFor(() => {
      expect(document.getElementById("first")?.textContent).toBe("a1a2");
      expect(document.getElementById("second")?.textContent).toBe("b1");
    });
    expect(rendered).toEqual([
      { titles: ["a1", "a2"], isAuthenticated: true },
      { titles: ["b1"], isAuthenticated: true },
    ]);
  });

  it("ログイン状態が無ければ未ログインとして扱う", async () => {
    document.body.innerHTML = `
      <script id="data-latest-cards-top" type="application/json">[{"title":"c1"}]</script>
      <div data-latest-cards="data-latest-cards-top"></div>
    `;

    await act(async () => {
      await import("../../../front/pages/LatestCardsPage");
    });

    await waitFor(() => {
      expect(rendered).toEqual([{ titles: ["c1"], isAuthenticated: false }]);
    });
  });
});
