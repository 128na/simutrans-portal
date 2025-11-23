import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import Input from "../apps/components/ui/Input";

describe("Input", () => {
  it("基本的なレンダリング", () => {
    render(<Input />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("labelが表示される", () => {
    render(<Input>ユーザー名</Input>);
    expect(screen.getByText("ユーザー名")).toBeInTheDocument();
  });

  it("valueとonChangeが正しく動作する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Input value="test" onChange={handleChange} />);
    
    const input = screen.getByRole("textbox");
    expect(input).toHaveValue("test");
    
    await user.clear(input);
    await user.type(input, "new value");
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled状態が正しく反映される", () => {
    render(<Input disabled />);
    expect(screen.getByRole("textbox")).toBeDisabled();
  });

  it("placeholderが設定される", () => {
    render(<Input placeholder="Enter text" />);
    expect(screen.getByPlaceholderText("Enter text")).toBeInTheDocument();
  });

  it("required属性が反映される", () => {
    render(<Input required />);
    expect(screen.getByRole("textbox")).toBeRequired();
  });

  it("type属性をカスタマイズできる", () => {
    render(<Input type="email" />);
    expect(screen.getByRole("textbox")).toHaveAttribute("type", "email");
  });

  it("カスタムclassNameが適用される", () => {
    render(<Input className="custom-input" />);
    expect(screen.getByRole("textbox")).toHaveClass("custom-input");
  });

  it("labelにカスタムclassNameが適用される", () => {
    render(<Input labelClassName="custom-label">Label</Input>);
    const label = screen.getByText("Label");
    expect(label).toHaveClass("custom-label");
  });

  it("childrenがない場合でも正しくレンダリングされる", () => {
    render(<Input />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });
});
