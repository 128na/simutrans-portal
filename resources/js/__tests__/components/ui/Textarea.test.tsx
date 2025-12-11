import Textarea from "@/components/ui/Textarea";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Textarea コンポーネント", () => {
  it("テキストエリアが表示される", () => {
    render(<Textarea />);
    expect(screen.getByRole("textbox")).toBeInTheDocument();
  });

  it("placeholder が設定される", () => {
    render(<Textarea placeholder="テスト入力" />);
    expect(screen.getByPlaceholderText("テスト入力")).toBeInTheDocument();
  });

  it("value が設定される", () => {
    render(<Textarea value="テスト値" readOnly />);
    expect(screen.getByRole("textbox")).toHaveValue("テスト値");
  });

  it("onChange イベントが発火する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Textarea onChange={handleChange} />);

    await user.type(screen.getByRole("textbox"), "test");
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled 状態で動作する", () => {
    render(<Textarea disabled />);
    expect(screen.getByRole("textbox")).toBeDisabled();
  });

  it("rows 属性が設定される", () => {
    render(<Textarea rows={5} />);
    expect(screen.getByRole("textbox")).toHaveAttribute("rows", "5");
  });

  it("maxLength が設定されたときカウンターが表示される", () => {
    render(<Textarea value="test" maxLength={100} readOnly />);
    expect(screen.getByText("4 / 100")).toBeInTheDocument();
  });

  it("maxLength がない場合カウンターは表示されない", () => {
    const { container } = render(<Textarea value="test" readOnly />);
    expect(container.textContent).not.toContain("/");
  });

  it("カスタムクラス名が適用される", () => {
    render(<Textarea className="custom-class" />);
    expect(screen.getByRole("textbox")).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<Textarea />);
    expect(screen.getByRole("textbox")).toHaveClass("v2-input");
  });

  it("複数行のテキストが正しくカウントされる", () => {
    const multilineText = "行1\n行2\n行3";
    render(<Textarea value={multilineText} maxLength={50} readOnly />);
    // "行1\n行2\n行3" は8文字（改行も1文字としてカウント）
    expect(screen.getByText("8 / 50")).toBeInTheDocument();
  });

  it("絵文字を含む文字列のカウントが正しい", () => {
    render(<Textarea value="👍😀テスト" maxLength={50} readOnly />);
    expect(screen.getByText("5 / 50")).toBeInTheDocument();
  });

  it("空の値の時カウンターが 0 を表示する", () => {
    render(<Textarea value="" maxLength={10} readOnly />);
    expect(screen.getByText("0 / 10")).toBeInTheDocument();
  });
});
