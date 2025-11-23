import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import Button from "../../apps/components/ui/Button";

describe("Button コンポーネント", () => {
  it("子要素がレンダリングされる", () => {
    render(<Button>クリック</Button>);
    expect(
      screen.getByRole("button", { name: "クリック" }),
    ).toBeInTheDocument();
  });

  it("disabled 属性が適用される", () => {
    render(<Button disabled>無効ボタン</Button>);
    expect(screen.getByRole("button")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Button className="custom-class">カスタム</Button>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("custom-class");
  });

  it("onClick ハンドラーが動作する", async () => {
    let clicked = false;
    render(<Button onClick={() => (clicked = true)}>テスト</Button>);
    const button = screen.getByRole("button");
    button.click();
    expect(clicked).toBe(true);
  });
});
