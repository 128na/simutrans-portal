import Checkbox from "@/components/ui/Checkbox";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("Checkbox コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<Checkbox />);
    expect(screen.getByRole("checkbox")).toBeInTheDocument();
  });

  it("ラベル付きでレンダリングされる", () => {
    render(<Checkbox>同意する</Checkbox>);
    expect(screen.getByText("同意する")).toBeInTheDocument();
    expect(screen.getByRole("checkbox")).toBeInTheDocument();
  });

  it("チェック状態の切り替えができる", async () => {
    const user = userEvent.setup();
    render(<Checkbox />);
    const checkbox = screen.getByRole("checkbox");
    expect(checkbox).not.toBeChecked();
    await user.click(checkbox);
    expect(checkbox).toBeChecked();
    await user.click(checkbox);
    expect(checkbox).not.toBeChecked();
  });

  it("初期チェック状態が設定される", () => {
    render(<Checkbox defaultChecked />);
    expect(screen.getByRole("checkbox")).toBeChecked();
  });

  it("制御されたチェック状態が動作する", async () => {
    const user = userEvent.setup();
    let checked = false;
    const Component = () => (
      <Checkbox
        checked={checked}
        onChange={(e) => (checked = e.target.checked)}
      >
        チェックボックス
      </Checkbox>
    );
    const { rerender } = render(<Component />);
    const checkbox = screen.getByRole("checkbox");
    expect(checkbox).not.toBeChecked();

    await user.click(checkbox);
    rerender(<Component />);
    expect(checked).toBe(true);
  });

  it("disabled 状態が適用される", () => {
    render(<Checkbox disabled />);
    expect(screen.getByRole("checkbox")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Checkbox className="custom-class" />);
    expect(screen.getByRole("checkbox")).toHaveClass("custom-class");
  });

  it("ラベルにカスタムクラス名が適用される", () => {
    render(<Checkbox labelClassName="custom-label-class">ラベル</Checkbox>);
    const label = screen.getByText("ラベル").closest("label");
    expect(label).toHaveClass("custom-label-class");
  });

  it("onChange ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let checked = false;
    render(<Checkbox onChange={(e) => (checked = e.target.checked)} />);
    const checkbox = screen.getByRole("checkbox");
    await user.click(checkbox);
    expect(checked).toBe(true);
  });
});
