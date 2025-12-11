import Checkbox from "@/components/ui/Checkbox";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Checkbox コンポーネント", () => {
  it("チェックボックスが表示される", () => {
    render(<Checkbox>テストラベル</Checkbox>);
    expect(screen.getByRole("checkbox")).toBeInTheDocument();
  });

  it("ラベルが表示される", () => {
    render(<Checkbox>チェックボックスラベル</Checkbox>);
    expect(screen.getByText("チェックボックスラベル")).toBeInTheDocument();
  });

  it("checked 状態が設定される", () => {
    render(
      <Checkbox checked readOnly>
        チェック済み
      </Checkbox>
    );
    expect(screen.getByRole("checkbox")).toBeChecked();
  });

  it("onChange イベントが発火する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Checkbox onChange={handleChange}>クリック</Checkbox>);

    await user.click(screen.getByRole("checkbox"));
    expect(handleChange).toHaveBeenCalled();
  });

  it("ラベルをクリックしてもチェックできる", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Checkbox onChange={handleChange}>ラベル</Checkbox>);

    await user.click(screen.getByText("ラベル"));
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled 状態で動作する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(
      <Checkbox onChange={handleChange} disabled>
        無効
      </Checkbox>
    );

    const checkbox = screen.getByRole("checkbox");
    expect(checkbox).toBeDisabled();

    await user.click(checkbox);
    expect(handleChange).not.toHaveBeenCalled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Checkbox className="custom-class">カスタム</Checkbox>);
    expect(screen.getByRole("checkbox")).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<Checkbox>デフォルト</Checkbox>);
    const checkbox = screen.getByRole("checkbox");
    expect(checkbox).toHaveClass("v2-checkbox");
    expect(checkbox).toHaveClass("peer");
  });

  it("ラベルに専用クラスが適用される", () => {
    render(<Checkbox>ラベル</Checkbox>);
    const label = screen.getByText("ラベル");
    expect(label).toHaveClass("v2-checkbox-label");
  });

  it("value 属性が設定される", () => {
    render(<Checkbox value="test-value">値あり</Checkbox>);
    expect(screen.getByRole("checkbox")).toHaveAttribute("value", "test-value");
  });

  it("name 属性が設定される", () => {
    render(<Checkbox name="test-name">名前あり</Checkbox>);
    expect(screen.getByRole("checkbox")).toHaveAttribute("name", "test-name");
  });
});
