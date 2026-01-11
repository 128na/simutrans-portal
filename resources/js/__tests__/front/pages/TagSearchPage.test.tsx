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
      <button data-testid="select-3" onClick={() => onChange([3])}>
        select 3
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

describe("TagSearchPage", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
    vi.resetModules();
  });

  it("renders hidden inputs from initial tags and updates on change", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags" data-tag-ids="[5]"></div>
      <script id="data-options" type="application/json">{"tags":[{"id":5,"name":"tag"}]}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(
        document.querySelectorAll('input[name="tagIds[]"][value="5"]').length
      ).toBe(1);
    });

    await act(async () => {
      fireEvent.click(document.querySelector('[data-testid="select-3"]')!);
    });

    await waitFor(() => {
      expect(
        document.querySelectorAll('input[name="tagIds[]"][value="3"]').length
      ).toBe(1);
    });
  });

  it("初期選択が複数の場合も正しく処理する", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags" data-tag-ids="[1, 2, 3]"></div>
      <script id="data-options" type="application/json">{"tags":[{"id":1,"name":"Tag1"},{"id":2,"name":"Tag2"},{"id":3,"name":"Tag3"}]}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="tagIds[]"]').length).toBe(
        3
      );
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("3");
    });
  });

  it("初期選択が空の場合も正常に動作する", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags" data-tag-ids="[]"></div>
      <script id="data-options" type="application/json">{"tags":[{"id":1,"name":"Tag1"}]}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="tagIds[]"]').length).toBe(
        0
      );
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("0");
    });
  });

  it("タグ追加時に hidden input が追加される", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags" data-tag-ids="[1]"></div>
      <script id="data-options" type="application/json">{"tags":[{"id":1,"name":"Tag1"},{"id":2,"name":"Tag2"}]}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="tagIds[]"]').length).toBe(
        1
      );
    });

    await act(async () => {
      fireEvent.click(document.querySelector('[data-testid="add-first"]')!);
    });

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="tagIds[]"]').length).toBe(
        2
      );
    });
  });

  it("全タグ削除時に hidden input が全て削除される", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags" data-tag-ids="[1, 2]"></div>
      <script id="data-options" type="application/json">{"tags":[{"id":1,"name":"Tag1"},{"id":2,"name":"Tag2"}]}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="tagIds[]"]').length).toBe(
        2
      );
    });

    await act(async () => {
      fireEvent.click(document.querySelector('[data-testid="remove-all"]')!);
    });

    await waitFor(() => {
      expect(document.querySelectorAll('input[name="tagIds[]"]').length).toBe(
        0
      );
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("0");
    });
  });

  it("SelectableSearchにオプションを正しく渡す", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags" data-tag-ids="[1]"></div>
      <script id="data-options" type="application/json">{"tags":[{"id":1,"name":"Tag1"},{"id":2,"name":"Tag2"},{"id":3,"name":"Tag3"}]}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="options-count"]')?.textContent
      ).toBe("3");
    });
  });

  it("不正なJSONの場合は空配列として扱う", async () => {
    document.body.innerHTML = `
      <div id="app-search-tags"></div>
      <script id="data-options" type="application/json">{}</script>
    `;

    await import("../../../front/pages/TagSearchPage");

    await waitFor(() => {
      expect(
        document.querySelector('[data-testid="selectable-search"]')
      ).toBeInTheDocument();
      expect(
        document.querySelector('[data-testid="selected-count"]')?.textContent
      ).toBe("0");
    });
  });
});
