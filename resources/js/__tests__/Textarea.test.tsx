import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import Textarea from "../apps/components/ui/Textarea";

describe("Textarea", () => {
  it("基本的なレンダリング", () => {
    render(<Textarea />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("labelが表示される", () => {
    render(<Textarea>説明文</Textarea>);
    expect(screen.getByText("説明文")).toBeInTheDocument();
  });

  it("valueとonChangeが正しく動作する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Textarea value="test text" onChange={handleChange} />);
    
    const textarea = screen.getByRole("textbox");
    expect(textarea).toHaveValue("test text");
    
    await user.clear(textarea);
    await user.type(textarea, "new text");
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled状態が正しく反映される", () => {
    render(<Textarea disabled />);
    expect(screen.getByRole("textbox")).toBeDisabled();
  });

  it("placeholderが設定される", () => {
    render(<Textarea placeholder="Enter description" />);
    expect(screen.getByPlaceholderText("Enter description")).toBeInTheDocument();
  });

  it("required属性が反映される", () => {
    render(<Textarea required />);
    expect(screen.getByRole("textbox")).toBeRequired();
  });

  it("rows属性を設定できる", () => {
    render(<Textarea rows={5} />);
    expect(screen.getByRole("textbox")).toHaveAttribute("rows", "5");
  });

  it("カスタムclassNameが適用される", () => {
    render(<Textarea className="custom-textarea" />);
    expect(screen.getByRole("textbox")).toHaveClass("custom-textarea");
  });

  it("labelにカスタムclassNameが適用される", () => {
    render(<Textarea labelClassName="custom-label">Label</Textarea>);
    const label = screen.getByText("Label");
    expect(label).toHaveClass("custom-label");
  });

  it("childrenがない場合でも正しくレンダリングされる", () => {
    render(<Textarea />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });
});
