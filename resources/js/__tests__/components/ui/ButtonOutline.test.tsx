import ButtonOutline from "@/components/ui/ButtonOutline";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("ButtonOutline コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<ButtonOutline>クリック</ButtonOutline>);
    expect(
      screen.getByRole("button", { name: "クリック" })
    ).toBeInTheDocument();
  });

  it("アウトラインスタイルが適用される", () => {
    render(<ButtonOutline>ボタン</ButtonOutline>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("button-primary", "button-outline");
  });

  it("disabled 属性が適用される", () => {
    render(<ButtonOutline disabled>無効ボタン</ButtonOutline>);
    expect(screen.getByRole("button")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<ButtonOutline className="custom-class">カスタム</ButtonOutline>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("custom-class");
  });

  it("onClick ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let clicked = false;
    render(
      <ButtonOutline onClick={() => (clicked = true)}>テスト</ButtonOutline>
    );
    const button = screen.getByRole("button");
    await user.click(button);
    expect(clicked).toBe(true);
  });

  it("type 属性がデフォルトで button になる", () => {
    render(<ButtonOutline>ボタン</ButtonOutline>);
    expect(screen.getByRole("button")).toHaveAttribute("type", "button");
  });
});
