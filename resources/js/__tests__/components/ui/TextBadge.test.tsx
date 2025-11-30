import TextBadge from "@/components/ui/TextBadge";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("TextBadge コンポーネント", () => {
  it("バッジが表示される", () => {
    render(<TextBadge>新着</TextBadge>);
    expect(screen.getByText("新着")).toBeInTheDocument();
  });

  it("空文字列の場合は何も表示しない", () => {
    const { container } = render(<TextBadge>{""}</TextBadge>);
    expect(container.firstChild).toBeNull();
  });

  it("カスタムクラス名が適用される", () => {
    render(<TextBadge className="custom-class">バッジ</TextBadge>);
    const badge = screen.getByText("バッジ");
    expect(badge).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<TextBadge>バッジ</TextBadge>);
    const badge = screen.getByText("バッジ");
    expect(badge).toHaveClass("text-[.75rem]");
    expect(badge).toHaveClass("bg-gray-500");
    expect(badge).toHaveClass("text-white");
  });

  it("複数のバッジが並べて表示できる", () => {
    render(
      <>
        <TextBadge>バッジ1</TextBadge>
        <TextBadge>バッジ2</TextBadge>
      </>
    );
    expect(screen.getByText("バッジ1")).toBeInTheDocument();
    expect(screen.getByText("バッジ2")).toBeInTheDocument();
  });
});
