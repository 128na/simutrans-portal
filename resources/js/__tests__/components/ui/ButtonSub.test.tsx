import ButtonSub from "@/components/ui/ButtonSub";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("ButtonSub コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<ButtonSub>サブボタン</ButtonSub>);
    expect(
      screen.getByRole("button", { name: "サブボタン" })
    ).toBeInTheDocument();
  });

  it("サブスタイルが適用される", () => {
    render(<ButtonSub>ボタン</ButtonSub>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("bg-gray-500");
    expect(button).toHaveClass("text-sm");
  });

  it("disabled 属性が適用される", () => {
    render(<ButtonSub disabled>無効ボタン</ButtonSub>);
    expect(screen.getByRole("button")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<ButtonSub className="custom-class">カスタム</ButtonSub>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("custom-class");
  });

  it("onClick ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let clicked = false;
    render(<ButtonSub onClick={() => (clicked = true)}>テスト</ButtonSub>);
    const button = screen.getByRole("button");
    await user.click(button);
    expect(clicked).toBe(true);
  });

  it("type 属性がデフォルトで button になる", () => {
    render(<ButtonSub>ボタン</ButtonSub>);
    expect(screen.getByRole("button")).toHaveAttribute("type", "button");
  });
});
