import Input from "@/components/ui/Input";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("Input コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<Input />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("ラベル付きでレンダリングされる", () => {
    render(<Input>ユーザー名</Input>);
    expect(screen.getByText("ユーザー名")).toBeInTheDocument();
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("placeholder が設定される", () => {
    render(<Input placeholder="名前を入力" />);
    expect(screen.getByPlaceholderText("名前を入力")).toBeInTheDocument();
  });

  it("値の入力ができる", async () => {
    const user = userEvent.setup();
    render(<Input />);
    const input = screen.getByRole("textbox");
    await user.type(input, "テスト入力");
    expect(input).toHaveValue("テスト入力");
  });

  it("disabled 状態が適用される", () => {
    render(<Input disabled />);
    expect(screen.getByRole("textbox")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Input className="custom-class" />);
    expect(screen.getByRole("textbox")).toHaveClass("custom-class");
  });

  it("ラベルにカスタムクラス名が適用される", () => {
    render(<Input labelClassName="custom-label-class">ラベル</Input>);
    const label = screen.getByText("ラベル");
    expect(label).toHaveClass("custom-label-class");
  });

  it("type 属性が設定される", () => {
    render(<Input type="email" />);
    expect(screen.getByRole("textbox")).toHaveAttribute("type", "email");
  });

  it("required 属性が適用される", () => {
    render(<Input required />);
    expect(screen.getByRole("textbox")).toBeRequired();
  });

  it("onChange ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let value = "";
    render(<Input onChange={(e) => (value = e.target.value)} />);
    const input = screen.getByRole("textbox");
    await user.type(input, "test");
    expect(value).toBe("test");
  });
});
