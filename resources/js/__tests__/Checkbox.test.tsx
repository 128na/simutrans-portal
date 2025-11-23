import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import Checkbox from "../apps/components/ui/Checkbox";

describe("Checkbox", () => {
  it("基本的なレンダリング", () => {
    render(<Checkbox>同意する</Checkbox>);
    expect(screen.getByRole("checkbox")).toBeInTheDocument();
    expect(screen.getByText("同意する")).toBeInTheDocument();
  });

  it("チェック状態が切り替わる", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Checkbox onChange={handleChange}>選択</Checkbox>);
    
    const checkbox = screen.getByRole("checkbox");
    expect(checkbox).not.toBeChecked();
    
    await user.click(checkbox);
    expect(handleChange).toHaveBeenCalled();
  });

  it("checked属性が正しく反映される", () => {
    render(<Checkbox checked>選択済み</Checkbox>);
    expect(screen.getByRole("checkbox")).toBeChecked();
  });

  it("disabled状態が正しく反映される", () => {
    render(<Checkbox disabled>無効</Checkbox>);
    expect(screen.getByRole("checkbox")).toBeDisabled();
  });

  it("カスタムclassNameが適用される", () => {
    render(<Checkbox className="custom-checkbox">Test</Checkbox>);
    expect(screen.getByRole("checkbox")).toHaveClass("custom-checkbox");
  });

  it("labelにカスタムclassNameが適用される", () => {
    render(<Checkbox labelClassName="custom-label">Label</Checkbox>);
    const label = screen.getByText("Label").closest("label");
    expect(label).toHaveClass("custom-label");
  });

  it("childrenがない場合でも正しくレンダリングされる", () => {
    render(<Checkbox />);
    expect(screen.getByRole("checkbox")).toBeInTheDocument();
  });

  it("type属性が常にcheckboxになる", () => {
    render(<Checkbox>Test</Checkbox>);
    expect(screen.getByRole("checkbox")).toHaveAttribute("type", "checkbox");
  });
});
