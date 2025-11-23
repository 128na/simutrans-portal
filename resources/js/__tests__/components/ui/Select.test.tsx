import Select from "@/components/ui/Select";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("Select コンポーネント", () => {
  const mockOptions = {
    option1: "オプション1",
    option2: "オプション2",
    option3: "オプション3",
  };

  it("基本的なレンダリング", () => {
    render(<Select options={mockOptions} />);
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });

  it("ラベル付きでレンダリングされる", () => {
    render(<Select options={mockOptions}>カテゴリー</Select>);
    expect(screen.getByText("カテゴリー")).toBeInTheDocument();
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });

  it("オプションが表示される", () => {
    render(<Select options={mockOptions} />);
    expect(
      screen.getByRole("option", { name: "オプション1" }),
    ).toBeInTheDocument();
    expect(
      screen.getByRole("option", { name: "オプション2" }),
    ).toBeInTheDocument();
    expect(
      screen.getByRole("option", { name: "オプション3" }),
    ).toBeInTheDocument();
  });

  it("選択ができる", async () => {
    const user = userEvent.setup();
    render(<Select options={mockOptions} />);
    const select = screen.getByRole("combobox");
    await user.selectOptions(select, "option2");
    expect(select).toHaveValue("option2");
  });

  it("初期値が設定される", () => {
    render(
      <Select options={mockOptions} value="option2" onChange={() => {}} />,
    );
    expect(screen.getByRole("combobox")).toHaveValue("option2");
  });

  it("disabled 状態が適用される", () => {
    render(<Select options={mockOptions} disabled />);
    expect(screen.getByRole("combobox")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Select options={mockOptions} className="custom-class" />);
    expect(screen.getByRole("combobox")).toHaveClass("custom-class");
  });

  it("ラベルにカスタムクラス名が適用される", () => {
    render(
      <Select options={mockOptions} labelClassName="custom-label-class">
        ラベル
      </Select>,
    );
    const label = screen.getByText("ラベル");
    expect(label).toHaveClass("custom-label-class");
  });

  it("onChange ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let value = "";
    render(
      <Select
        options={mockOptions}
        onChange={(e) => (value = e.target.value)}
      />,
    );
    const select = screen.getByRole("combobox");
    await user.selectOptions(select, "option3");
    expect(value).toBe("option3");
  });
});
