import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import Button from "@/components/ui/Button";

describe("Button", () => {
  it("テキストが表示される", () => {
    render(<Button>クリック</Button>);
    expect(screen.getByRole("button")).toHaveTextContent("クリック");
  });

  it("クリックイベントが発火する", async () => {
    const user = userEvent.setup();
    const handleClick = vi.fn();
    render(<Button onClick={handleClick}>クリック</Button>);

    await user.click(screen.getByRole("button"));
    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it("disabled時はクリックできない", async () => {
    const user = userEvent.setup();
    const handleClick = vi.fn();
    render(
      <Button onClick={handleClick} disabled>
        クリック
      </Button>
    );

    await user.click(screen.getByRole("button"));
    expect(handleClick).not.toHaveBeenCalled();
  });

  it("disabled時のスタイルが適用される", () => {
    render(<Button disabled>無効</Button>);
    const button = screen.getByRole("button");
    expect(button).toBeDisabled();
    expect(button).toHaveClass("button-primary");
  });

  it("カスタムクラスが追加できる", () => {
    render(<Button className="custom-class">クリック</Button>);
    expect(screen.getByRole("button")).toHaveClass("custom-class");
  });

  it("type属性が指定できる", () => {
    render(<Button type="submit">送信</Button>);
    expect(screen.getByRole("button")).toHaveAttribute("type", "submit");
  });

  it("デフォルトのtypeはbutton", () => {
    render(<Button>クリック</Button>);
    expect(screen.getByRole("button")).toHaveAttribute("type", "button");
  });

  it("ブランドカラーのスタイルが適用される", () => {
    render(<Button>クリック</Button>);
    expect(screen.getByRole("button")).toHaveClass("button-primary");
  });
});
