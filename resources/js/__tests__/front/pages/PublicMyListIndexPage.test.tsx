import axios from "axios";
import { describe, expect, it, beforeEach, vi } from "vitest";
import { act, waitFor } from "@testing-library/react";

vi.mock("axios");
vi.mock("@/components/AppWrapper", () => ({
  AppWrapper: ({ children }: { children: React.ReactNode }) => <>{children}</>,
}));

const mockAxios = axios as unknown as { get: ReturnType<typeof vi.fn> };

describe("PublicMyListIndexPage", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
    vi.resetModules();
    mockAxios.get = vi.fn();
    window.history.replaceState({}, "", "/mylist");
  });

  it("公開マイリスト一覧を表示する", async () => {
    document.body.innerHTML = '<div id="app-public-mylist-index"></div>';

    mockAxios.get.mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            title: "公開リストA",
            note: "メモA",
            is_public: true,
            slug: "public-a",
            items_count: 2,
            created_at: "2026/02/10 10:00",
            updated_at: "2026/02/11 10:00",
          },
        ],
        links: { first: null, last: null, prev: null, next: null },
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: 20,
          total: 1,
          from: 1,
          to: 1,
        },
      },
    });

    await act(async () => {
      await import("../../../front/pages/PublicMyListIndexPage");
    });

    await waitFor(() => {
      expect(document.body.textContent).toContain("公開リストA");
      expect(document.body.textContent).toContain("メモA");
    });

    expect(mockAxios.get).toHaveBeenCalledWith("/api/v1/mylist/public", {
      params: {
        page: 1,
        per_page: 20,
        sort: "updated_at:desc",
      },
    });
  });

  it("クエリのpageをAPIパラメータに反映する", async () => {
    document.body.innerHTML = '<div id="app-public-mylist-index"></div>';
    window.history.replaceState({}, "", "/mylist?page=2");

    mockAxios.get.mockResolvedValue({
      data: {
        data: [],
        links: { first: null, last: null, prev: null, next: null },
        meta: {
          current_page: 2,
          last_page: 2,
          per_page: 20,
          total: 0,
          from: null,
          to: null,
        },
      },
    });

    await act(async () => {
      await import("../../../front/pages/PublicMyListIndexPage");
    });

    await waitFor(() => {
      expect(mockAxios.get).toHaveBeenCalled();
    });

    expect(mockAxios.get).toHaveBeenCalledWith("/api/v1/mylist/public", {
      params: {
        page: 2,
        per_page: 20,
        sort: "updated_at:desc",
      },
    });
  });

  it("空リストの場合はメッセージを表示する", async () => {
    document.body.innerHTML = '<div id="app-public-mylist-index"></div>';

    mockAxios.get.mockResolvedValue({
      data: {
        data: [],
        links: { first: null, last: null, prev: null, next: null },
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: 20,
          total: 0,
          from: null,
          to: null,
        },
      },
    });

    await act(async () => {
      await import("../../../front/pages/PublicMyListIndexPage");
    });

    await waitFor(() => {
      expect(document.body.textContent).toContain(
        "公開マイリストがありません。"
      );
    });
  });
});
