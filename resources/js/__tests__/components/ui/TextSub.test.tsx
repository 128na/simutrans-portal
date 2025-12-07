import TextSub from "@/components/ui/TextSub";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("TextSub コンポーネント", () => {
  it("サブテキストが表示される", () => {
    render(<TextSub>補足説明</TextSub>);
    expect(screen.getByText("補足説明")).toBeInTheDocument();
  });

  it("空文字列の場合は何も表示しない", () => {
    const { container } = render(<TextSub>{""}</TextSub>);
    expect(container.firstChild).toBeNull();
  });

  it("カスタムクラス名が適用される", () => {
    render(<TextSub className="custom-class">テキスト</TextSub>);
    const text = screen.getByText("テキスト");
    expect(text).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<TextSub>テキスト</TextSub>);
    const text = screen.getByText("テキスト");
    expect(text).toHaveClass("text-sm");
    expect(text).toHaveClass("text-c-sub");
  });

  it("長いテキストが表示される", () => {
    const longText = "これは非常に長いサブテキストです。".repeat(10);
    render(<TextSub>{longText}</TextSub>);
    expect(screen.getByText(longText)).toBeInTheDocument();
  });
});
