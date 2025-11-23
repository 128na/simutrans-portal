import ButtonClose from "@/components/ui/ButtonClose";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("ButtonClose コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<ButtonClose />);
    expect(screen.getByRole("button")).toBeInTheDocument();
  });

  it("閉じるアイコンが含まれる", () => {
    render(<ButtonClose />);
    const button = screen.getByRole("button");
    const svg = button.querySelector("svg");
    expect(svg).toBeInTheDocument();
  });

  it("disabled 属性が適用される", () => {
    render(<ButtonClose disabled />);
    expect(screen.getByRole("button")).toBeDisabled();
  });

  it("カスタムクラス名が適用される", () => {
    render(<ButtonClose className="custom-class" />);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("custom-class");
  });

  it("onClick ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let clicked = false;
    render(<ButtonClose onClick={() => (clicked = true)} />);
    const button = screen.getByRole("button");
    await user.click(button);
    expect(clicked).toBe(true);
  });

  it("type 属性がデフォルトで button になる", () => {
    render(<ButtonClose />);
    expect(screen.getByRole("button")).toHaveAttribute("type", "button");
  });
});
