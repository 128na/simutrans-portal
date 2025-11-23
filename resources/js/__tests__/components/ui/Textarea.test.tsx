import Textarea from "@/components/ui/Textarea";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("Textarea コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<Textarea />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("ラベル付きでレンダリングされる", () => {
    render(<Textarea>説明</Textarea>);
    expect(screen.getByText("説明")).toBeInTheDocument();
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("placeholder が設定される", () => {
    render(<Textarea placeholder="詳細を入力" />);
    expect(screen.getByPlaceholderText("詳細を入力")).toBeInTheDocument();
  });

  it("値の入力ができる", async () => {
    const user = userEvent.setup();
    render(<Textarea />);
    const textarea = screen.getByRole("textbox");
    await user.type(textarea, "テスト入力");
    expect(textarea).toHaveValue("テスト入力");
  });

  it("複数行のテキストが入力できる", async () => {
    const user = userEvent.setup();
    render(<Textarea />);
    const textarea = screen.getByRole("textbox");
    const multilineText = "行1\n行2\n行3";
    await user.type(textarea, multilineText);
    expect(textarea).toHaveValue(multilineText);
  });

  it("disabled 状態が適用される", () => {
    render(<Textarea disabled />);
    expect(screen.getByRole("textbox")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Textarea className="custom-class" />);
    expect(screen.getByRole("textbox")).toHaveClass("custom-class");
  });

  it("ラベルにカスタムクラス名が適用される", () => {
    render(<Textarea labelClassName="custom-label-class">ラベル</Textarea>);
    const label = screen.getByText("ラベル");
    expect(label).toHaveClass("custom-label-class");
  });

  it("rows 属性が設定される", () => {
    render(<Textarea rows={5} />);
    expect(screen.getByRole("textbox")).toHaveAttribute("rows", "5");
  });

  it("onChange ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let value = "";
    render(<Textarea onChange={(e) => (value = e.target.value)} />);
    const textarea = screen.getByRole("textbox");
    await user.type(textarea, "test");
    expect(value).toBe("test");
  });
});
