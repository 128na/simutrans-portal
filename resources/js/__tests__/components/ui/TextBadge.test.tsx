import TextBadge from "@/components/ui/TextBadge";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("TextBadge コンポーネント", () => {
  it("バッジが表示される", () => {
    render(<TextBadge>テストバッジ</TextBadge>);
    expect(screen.getByText("テストバッジ")).toBeInTheDocument();
  });

  it("空文字列の場合は何も表示されない", () => {
    const { container } = render(<TextBadge>{""}</TextBadge>);
    expect(container.firstChild).toBeNull();
  });

  it("variant が適用される", () => {
    const { rerender } = render(
      <TextBadge variant="primary">Primary</TextBadge>
    );
    expect(screen.getByText("Primary")).toHaveClass("v2-badge-primary");

    rerender(<TextBadge variant="danger">Danger</TextBadge>);
    expect(screen.getByText("Danger")).toHaveClass("v2-badge-danger");

    rerender(<TextBadge variant="success">Success</TextBadge>);
    expect(screen.getByText("Success")).toHaveClass("v2-badge-success");

    rerender(<TextBadge variant="warn">Warn</TextBadge>);
    expect(screen.getByText("Warn")).toHaveClass("v2-badge-warn");

    rerender(<TextBadge variant="info">Info</TextBadge>);
    expect(screen.getByText("Info")).toHaveClass("v2-badge-info");
  });

  it("デフォルトで sub variant が適用される", () => {
    render(<TextBadge>デフォルト</TextBadge>);
    expect(screen.getByText("デフォルト")).toHaveClass("v2-badge-sub");
  });

  it("カスタムクラス名が適用される", () => {
    render(<TextBadge className="custom-class">カスタム</TextBadge>);
    expect(screen.getByText("カスタム")).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<TextBadge>バッジ</TextBadge>);
    expect(screen.getByText("バッジ")).toHaveClass("v2-badge");
  });

  it("main variant が適用される", () => {
    render(<TextBadge variant="main">メイン</TextBadge>);
    expect(screen.getByText("メイン")).toHaveClass("v2-badge-main");
  });

  it("数値も表示できる", () => {
    render(<TextBadge>{123}</TextBadge>);
    expect(screen.getByText("123")).toBeInTheDocument();
  });

  it("React要素も子要素にできる", () => {
    render(
      <TextBadge>
        <strong>強調</strong>
      </TextBadge>
    );
    expect(screen.getByText("強調")).toBeInTheDocument();
  });
});
