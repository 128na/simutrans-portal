import Input from "@/components/ui/Input";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Input コンポーネント", () => {
  it("入力フィールドが表示される", () => {
    render(<Input />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("placeholder が設定される", () => {
    render(<Input placeholder="テスト入力" />);
    expect(screen.getByPlaceholderText("テスト入力")).toBeInTheDocument();
  });

  it("value が設定される", () => {
    render(<Input value="テスト値" readOnly />);
    expect(screen.getByRole("textbox")).toHaveValue("テスト値");
  });

  it("onChange イベントが発火する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Input onChange={handleChange} />);

    await user.type(screen.getByRole("textbox"), "test");
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled 状態で動作する", () => {
    render(<Input disabled />);
    expect(screen.getByRole("textbox")).toBeDisabled();
  });

  it("type 属性が設定される", () => {
    render(<Input type="email" />);
    expect(screen.getByRole("textbox")).toHaveAttribute("type", "email");

    const { container: passwordContainer } = render(<Input type="password" />);
    const passwordInput = passwordContainer.querySelector(
      'input[type="password"]'
    );
    expect(passwordInput).toHaveAttribute("type", "password");
  });

  it("maxLength が設定されたときカウンターが表示される", () => {
    render(<Input value="test" maxLength={10} readOnly />);
    expect(screen.getByText("4 / 10")).toBeInTheDocument();
  });

  it("maxLength がない場合カウンターは表示されない", () => {
    const { container } = render(<Input value="test" readOnly />);
    expect(container.textContent).not.toContain("/");
  });

  it("カスタムカウンター関数が使用される", () => {
    const customCounter = (value: string) => value.split(",").length;
    render(
      <Input value="a,b,c" maxLength={5} counter={customCounter} readOnly />
    );
    expect(screen.getByText("3 / 5")).toBeInTheDocument();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Input className="custom-class" />);
    expect(screen.getByRole("textbox")).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<Input />);
    expect(screen.getByRole("textbox")).toHaveClass("v2-input");
  });

  it("type に応じたクラスが適用される", () => {
    render(<Input type="email" />);
    expect(screen.getByRole("textbox")).toHaveClass("v2-input-email");
  });

  it("絵文字を含む文字列のカウントが正しい", () => {
    render(<Input value="👍😀" maxLength={10} readOnly />);
    expect(screen.getByText("2 / 10")).toBeInTheDocument();
  });
});
