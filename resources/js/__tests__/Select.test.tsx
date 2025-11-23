import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import Select from "../apps/components/ui/Select";

describe("Select", () => {
  const options = {
    option1: "オプション1",
    option2: "オプション2",
    option3: "オプション3",
  };

  it("基本的なレンダリング", () => {
    render(<Select options={options} />);
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });

  it("labelが表示される", () => {
    render(<Select options={options}>カテゴリー</Select>);
    expect(screen.getByText("カテゴリー")).toBeInTheDocument();
  });

  it("optionsが正しく表示される", () => {
    render(<Select options={options} />);
    expect(screen.getByRole("option", { name: "オプション1" })).toBeInTheDocument();
    expect(screen.getByRole("option", { name: "オプション2" })).toBeInTheDocument();
    expect(screen.getByRole("option", { name: "オプション3" })).toBeInTheDocument();
  });

  it("valueが正しく反映される", () => {
    render(<Select options={options} value="option2" />);
    expect(screen.getByRole("combobox")).toHaveValue("option2");
  });

  it("onChangeハンドラーが動作する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Select options={options} onChange={handleChange} />);
    
    await user.selectOptions(screen.getByRole("combobox"), "option2");
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled状態が正しく反映される", () => {
    render(<Select options={options} disabled />);
    expect(screen.getByRole("combobox")).toBeDisabled();
  });

  it("カスタムclassNameが適用される", () => {
    render(<Select options={options} className="custom-select" />);
    expect(screen.getByRole("combobox")).toHaveClass("custom-select");
  });

  it("labelにカスタムclassNameが適用される", () => {
    render(<Select options={options} labelClassName="custom-label">Label</Select>);
    const label = screen.getByText("Label");
    expect(label).toHaveClass("custom-label");
  });

  it("空のoptionsでも正しくレンダリングされる", () => {
    render(<Select options={{}} />);
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });
});
