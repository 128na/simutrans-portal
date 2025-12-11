import Select from "@/components/ui/Select";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Select コンポーネント", () => {
  const mockOptions = {
    option1: "オプション1",
    option2: "オプション2",
    option3: "オプション3",
  };

  it("セレクトボックスが表示される", () => {
    render(<Select options={mockOptions} />);
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });

  it("オプションが正しく表示される", () => {
    render(<Select options={mockOptions} />);

    expect(
      screen.getByRole("option", { name: "オプション1" })
    ).toBeInTheDocument();
    expect(
      screen.getByRole("option", { name: "オプション2" })
    ).toBeInTheDocument();
    expect(
      screen.getByRole("option", { name: "オプション3" })
    ).toBeInTheDocument();
  });

  it("option の value が正しく設定される", () => {
    render(<Select options={mockOptions} />);

    const options = screen.getAllByRole("option");
    expect(options[0]).toHaveValue("option1");
    expect(options[1]).toHaveValue("option2");
    expect(options[2]).toHaveValue("option3");
  });

  it("value が設定される", () => {
    render(<Select options={mockOptions} value="option2" />);
    expect(screen.getByRole("combobox")).toHaveValue("option2");
  });

  it("onChange イベントが発火する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Select options={mockOptions} onChange={handleChange} />);

    await user.selectOptions(screen.getByRole("combobox"), "option2");
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled 状態で動作する", () => {
    render(<Select options={mockOptions} disabled />);
    expect(screen.getByRole("combobox")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Select options={mockOptions} className="custom-class" />);
    expect(screen.getByRole("combobox")).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<Select options={mockOptions} />);
    const select = screen.getByRole("combobox");
    expect(select).toHaveClass("v2-input");
    expect(select).toHaveClass("v2-input-select");
  });

  it("空のオプションでもエラーにならない", () => {
    render(<Select options={{}} />);
    expect(screen.getByRole("combobox")).toBeInTheDocument();
    expect(screen.queryAllByRole("option")).toHaveLength(0);
  });
});
