import { describe, expect, it } from "vitest";
import { fireEvent, waitFor, act } from "@testing-library/react";

vi.mock("../../../components/form/SelectableSearch", () => ({
  SelectableSearch: ({ onChange }: { onChange: (ids: number[]) => void }) => (
    <button data-testid="select" onClick={() => onChange([2])}>
      select
    </button>
  ),
}));

vi.mock("../../../components/ErrorBoundary", () => ({
  ErrorBoundary: ({ children }: { children: React.ReactNode }) => (
    <>{children}</>
  ),
}));

describe("UserSearchPage", () => {
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
      fireEvent.click(document.querySelector('[data-testid="select"]')!);
    });

    await waitFor(() => {
      expect(
        document.querySelectorAll('input[name="userIds[]"][value="2"]').length
      ).toBe(1);
    });
  });
});
