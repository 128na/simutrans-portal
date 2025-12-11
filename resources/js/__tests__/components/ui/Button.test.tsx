import Button from "@/components/ui/Button";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Button コンポーネント", () => {
  it("ボタンが表示される", () => {
    render(<Button>テストボタン</Button>);
    expect(screen.getByText("テストボタン")).toBeInTheDocument();
  });

  it("クリックイベントが発火する", async () => {
    const user = userEvent.setup();
    const handleClick = vi.fn();
    render(<Button onClick={handleClick}>クリック</Button>);

    await user.click(screen.getByRole("button"));
    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it("disabled 状態で動作する", async () => {
    const user = userEvent.setup();
    const handleClick = vi.fn();
    render(
      <Button onClick={handleClick} disabled>
        無効ボタン
      </Button>
    );

    const button = screen.getByRole("button");
    expect(button).toBeDisabled();

    await user.click(button);
    expect(handleClick).not.toHaveBeenCalled();
  });

  it("variant が適用される", () => {
    const { rerender } = render(<Button variant="primary">Primary</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-primary");

    rerender(<Button variant="danger">Danger</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-danger");

    rerender(<Button variant="success">Success</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-success");
  });

  it("outline variant が適用される", () => {
    render(<Button variant="primaryOutline">Outline</Button>);
    const button = screen.getByRole("button");
    expect(button).toHaveClass("v2-button-outline");
    expect(button).toHaveClass("v2-button-primary-outline");
  });

  it("size が適用される", () => {
    const { rerender } = render(<Button size="sm">Small</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-sm");

    rerender(<Button size="md">Medium</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-md");

    rerender(<Button size="lg">Large</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-lg");
  });

  it("カスタムクラス名が適用される", () => {
    render(<Button className="custom-class">カスタム</Button>);
    expect(screen.getByRole("button")).toHaveClass("custom-class");
  });

  it("type 属性がデフォルトで button になる", () => {
    render(<Button>ボタン</Button>);
    expect(screen.getByRole("button")).toHaveAttribute("type", "button");
  });

  it("type 属性を上書きできる", () => {
    render(<Button type="submit">送信</Button>);
    expect(screen.getByRole("button")).toHaveAttribute("type", "submit");
  });

  it("disabled 時に専用クラスが適用される", () => {
    render(<Button disabled>無効</Button>);
    expect(screen.getByRole("button")).toHaveClass("v2-button-disabled");
  });
});
