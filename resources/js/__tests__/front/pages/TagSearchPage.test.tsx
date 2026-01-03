import { describe, expect, it } from "vitest";
import { fireEvent, waitFor, act } from "@testing-library/react";

vi.mock("../../../components/form/SelectableSearch", () => ({
  SelectableSearch: ({ onChange }: { onChange: (ids: number[]) => void }) => (
    <button data-testid="select" onClick={() => onChange([3])}>
      select
    </button>
  ),
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("TagSearchPage", () => {
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
      fireEvent.click(document.querySelector('[data-testid="select"]')!);
    });

    await waitFor(() => {
      expect(
        document.querySelectorAll('input[name="tagIds[]"][value="3"]').length
      ).toBe(1);
    });
  });
});
