import ButtonDanger from "@/components/ui/ButtonDanger";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("ButtonDanger コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<ButtonDanger>削除</ButtonDanger>);
    expect(screen.getByRole("button", { name: "削除" })).toBeInTheDocument();
  });

  it("危険スタイルが適用される", () => {
    render(<ButtonDanger>ボタン</ButtonDanger>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("bg-red-600");
  });

  it("disabled 属性が適用される", () => {
    render(<ButtonDanger disabled>無効ボタン</ButtonDanger>);
    expect(screen.getByRole("button")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<ButtonDanger className="custom-class">カスタム</ButtonDanger>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("custom-class");
  });

  it("onClick ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let clicked = false;
    render(
      <ButtonDanger onClick={() => (clicked = true)}>テスト</ButtonDanger>
    );
    const button = screen.getByRole("button");
    await user.click(button);
    expect(clicked).toBe(true);
  });
});
