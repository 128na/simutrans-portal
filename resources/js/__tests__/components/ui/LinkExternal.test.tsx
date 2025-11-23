import LinkExternal from "@/components/ui/LinkExternal";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("LinkExternal コンポーネント", () => {
  it("外部リンクが表示される", () => {
    render(<LinkExternal href="https://example.com">外部サイト</LinkExternal>);
    expect(screen.getByText("外部サイト")).toBeInTheDocument();
  });

  it("外部リンクアイコンが表示される", () => {
    render(<LinkExternal href="https://example.com">リンク</LinkExternal>);
    expect(screen.getByText("↗")).toBeInTheDocument();
  });

  it("href 属性が設定される", () => {
    render(<LinkExternal href="https://example.com">リンク</LinkExternal>);
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("href", "https://example.com");
  });

  it("カスタムクラス名が適用される", () => {
    render(
      <LinkExternal href="https://example.com" className="custom-class">
        リンク
      </LinkExternal>,
    );
    const span = screen.getByText("リンク");
    expect(span).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<LinkExternal href="https://example.com">リンク</LinkExternal>);
    const span = screen.getByText("リンク");
    expect(span).toHaveClass("underline");
    expect(span).toHaveClass("text-brand");
  });

  it("target 属性が設定される", () => {
    render(
      <LinkExternal href="https://example.com" target="_blank">
        リンク
      </LinkExternal>,
    );
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("target", "_blank");
  });
});
