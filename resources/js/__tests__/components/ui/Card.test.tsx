import Card from "@/components/ui/Card";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("Card コンポーネント", () => {
  it("カードが表示される", () => {
    render(<Card>テストカード</Card>);
    expect(screen.getByText("テストカード")).toBeInTheDocument();
  });

  it("variant が適用される", () => {
    const { container, rerender } = render(
      <Card variant="primary">Primary</Card>
    );
    let card = container.querySelector(".v2-card-primary");
    expect(card).toBeInTheDocument();

    rerender(<Card variant="danger">Danger</Card>);
    card = container.querySelector(".v2-card-danger");
    expect(card).toBeInTheDocument();

    rerender(<Card variant="success">Success</Card>);
    card = container.querySelector(".v2-card-success");
    expect(card).toBeInTheDocument();

    rerender(<Card variant="warn">Warn</Card>);
    card = container.querySelector(".v2-card-warn");
    expect(card).toBeInTheDocument();

    rerender(<Card variant="info">Info</Card>);
    card = container.querySelector(".v2-card-info");
    expect(card).toBeInTheDocument();
  });

  it("デフォルトで primary variant が適用される", () => {
    const { container } = render(<Card>デフォルト</Card>);
    expect(container.querySelector(".v2-card-primary")).toBeInTheDocument();
  });

  it("カスタムクラス名が適用される", () => {
    const { container } = render(<Card className="custom-card">カスタム</Card>);
    expect(container.querySelector(".custom-card")).toBeInTheDocument();
  });

  it("デフォルトのスタイルが適用される", () => {
    const { container } = render(<Card>カード</Card>);
    expect(container.querySelector(".v2-card")).toBeInTheDocument();
  });

  it("main variant が適用される", () => {
    const { container } = render(<Card variant="main">メイン</Card>);
    expect(container.querySelector(".v2-card-main")).toBeInTheDocument();
  });

  it("sub variant が適用される", () => {
    const { container } = render(<Card variant="sub">サブ</Card>);
    expect(container.querySelector(".v2-card-sub")).toBeInTheDocument();
  });

  it("複数の子要素を含むことができる", () => {
    render(
      <Card>
        <h2>タイトル</h2>
        <p>本文</p>
      </Card>
    );
    expect(screen.getByText("タイトル")).toBeInTheDocument();
    expect(screen.getByText("本文")).toBeInTheDocument();
  });

  it("div要素としてレンダリングされる", () => {
    const { container } = render(<Card>内容</Card>);
    expect(container.firstChild?.nodeName).toBe("DIV");
  });

  it("HTML属性を渡せる", () => {
    const { container } = render(
      <Card data-testid="test-card" id="my-card">
        カード
      </Card>
    );
    const card = container.querySelector("#my-card");
    expect(card).toBeInTheDocument();
    expect(card).toHaveAttribute("data-testid", "test-card");
  });
});
