import { describe, expect, it, beforeEach } from "vitest";
import { fireEvent, waitFor, act } from "@testing-library/react";

vi.mock("../../../components/form/SelectableSearch", () => ({
  SelectableSearch: ({
    options = [],
    selectedIds = [],
    onChange,
  }: {
    options?: Array<{ id: number; name: string }>;
    selectedIds?: number[];
    onChange: (ids: number[]) => void;
  }) => (
    <div data-testid="selectable-search">
      <div data-testid="selected-count">{selectedIds.length}</div>
      <div data-testid="options-count">{options.length}</div>
      <button data-testid="select-2" onClick={() => onChange([2])}>
        select 2
      </button>
      <button
        data-testid="add-first"
        onClick={() => onChange([...selectedIds, options[0]?.id])}
      >
        add first
      </button>
      <button data-testid="remove-all" onClick={() => onChange([])}>
        remove all
      </button>
    </div>
  ),
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("UserSearchPage", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
    vi.resetModules();
  });

  it("renders hidden inputs from initial ids and updates on change", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[1]"></div>
      <script id="data-options" type="application/json">{"users":[{"id":1,"name":"Alice"}]}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(
        document.querySelectorAll('input[name="userIds[]"][value="1"]').length
      ).toBe(1);
    });

    await act(async () => {
      fireEvent.click(document.querySelector('[data-testid="select-2"]')!);
    });

    await waitFor(() => {
      expect(
        document.querySelectorAll('input[name="userIds[]"][value="2"]').length
      ).toBe(1);
    });
  });

  it("初期選択が複数の場合も正しく処理する", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[1, 2, 3]"></div>
      <script id="data-options" type="application/json">{"users":[{"id":1,"name":"Alice"},{"id":2,"name":"Bob"},{"id":3,"name":"Charlie"}]}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="userIds[]"]').length).toBe(
        3
      );
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("3");
    });
  });

  it("初期選択が空の場合も正常に動作する", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[]"></div>
      <script id="data-options" type="application/json">{"users":[{"id":1,"name":"Alice"}]}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="userIds[]"]').length).toBe(
        0
      );
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("0");
    });
  });

  it("ユーザー追加時に hidden input が追加される", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[1]"></div>
      <script id="data-options" type="application/json">{"users":[{"id":1,"name":"Alice"},{"id":2,"name":"Bob"}]}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="userIds[]"]').length).toBe(
        1
      );
    });

    await act(async () => {
      fireEvent.click(document.querySelector('[data-testid="add-first"]')!);
    });

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="userIds[]"]').length).toBe(
        2
      );
    });
  });

  it("全ユーザー削除時に hidden input が全て削除される", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[1, 2]"></div>
      <script id="data-options" type="application/json">{"users":[{"id":1,"name":"Alice"},{"id":2,"name":"Bob"}]}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="userIds[]"]').length).toBe(
        2
      );
    });

    await act(async () => {
      fireEvent.click(document.querySelector('[data-testid="remove-all"]')!);
    });

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="userIds[]"]').length).toBe(
        0
      );
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("0");
    });
  });

  it("SelectableSearchにオプションを正しく渡す", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[1]"></div>
      <script id="data-options" type="application/json">{"users":[{"id":1,"name":"Alice"},{"id":2,"name":"Bob"},{"id":3,"name":"Charlie"}]}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="options-count"]')?.textContent
      ).toBe("3");
    });
  });

  it("不正なJSONの場合は空配列として扱う", async () => {
    document.body.innerHTML = `
      <div id="app-search-users"></div>
      <script id="data-options" type="application/json">{}</script>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="selectable-search"]')
      ).toBeInTheDocument();
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("0");
    });
  });

  it("data-options要素が存在しない場合も動作する", async () => {
    document.body.innerHTML = `
      <div id="app-search-users" data-user-ids="[]"></div>
    `;

    await import("../../../front/pages/UserSearchPage");

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="selectable-search"]')
      ).toBeInTheDocument();
    });
  });
});
