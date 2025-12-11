import ButtonClose from "@/components/ui/ButtonClose";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("ButtonClose コンポーネント", () => {
  it("閉じるボタンが表示される", () => {
    render(<ButtonClose />);
    expect(screen.getByRole("button")).toBeInTheDocument();
  });

  it("SVGアイコンが含まれる", () => {
    const { container } = render(<ButtonClose />);
    const svg = container.querySelector("svg");
    expect(svg).toBeInTheDocument();
    expect(svg).toHaveClass("w-5", "h-5");
  });

  it("クリックイベントが発火する", async () => {
    const user = userEvent.setup();
    const handleClick = vi.fn();
    render(<ButtonClose onClick={handleClick} />);

    await user.click(screen.getByRole("button"));
    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it("disabled 状態で動作する", async () => {
    const user = userEvent.setup();
    const handleClick = vi.fn();
    render(<ButtonClose onClick={handleClick} disabled />);

    const button = screen.getByRole("button");
    expect(button).toBeDisabled();

    await user.click(button);
    expect(handleClick).not.toHaveBeenCalled();
  });

  it("type 属性がデフォルトで button になる", () => {
    render(<ButtonClose />);
    expect(screen.getByRole("button")).toHaveAttribute("type", "button");
  });

  it("カスタムクラス名が適用される", () => {
    render(<ButtonClose className="custom-close" />);
    expect(screen.getByRole("button")).toHaveClass("custom-close");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<ButtonClose />);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("v2-text-sub");
    expect(button).toHaveClass("hover:rounded-lg");
    expect(button).toHaveClass("cursor-pointer");
  });

  it("aria-label を設定できる", () => {
    render(<ButtonClose aria-label="モーダルを閉じる" />);
    expect(screen.getByRole("button")).toHaveAttribute(
      "aria-label",
      "モーダルを閉じる"
    );
  });

  it("SVGのpathが正しく設定されている", () => {
    const { container } = render(<ButtonClose />);
    const path = container.querySelector("path");
    expect(path).toBeInTheDocument();
    expect(path).toHaveAttribute("fill-rule", "evenodd");
    expect(path).toHaveAttribute("clip-rule", "evenodd");
  });
});
