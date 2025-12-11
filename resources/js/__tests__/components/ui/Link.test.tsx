import Link from "@/components/ui/Link";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("Link コンポーネント", () => {
  it("リンクが表示される", () => {
    render(<Link href="/test">テストリンク</Link>);
    expect(screen.getByText("テストリンク")).toBeInTheDocument();
  });

  it("href 属性が設定される", () => {
    render(<Link href="/test">リンク</Link>);
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("href", "/test");
  });

  it("カスタムクラス名が適用される", () => {
    render(
      <Link href="/test" className="custom-class">
        リンク
      </Link>
    );
    const span = screen.getByText("リンク");
    expect(span).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<Link href="/test">リンク</Link>);
    const span = screen.getByText("リンク");
    expect(span).toHaveClass("v2-link");
  });

  it("target 属性が設定される", () => {
    render(
      <Link href="/test" target="_blank">
        リンク
      </Link>
    );
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("target", "_blank");
  });

  it("rel 属性が設定される", () => {
    render(
      <Link href="/test" rel="noopener">
        リンク
      </Link>
    );
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("rel", "noopener");
  });

  it("外部リンクの場合は自動的に target=_blank と rel が設定される", () => {
    render(<Link href="https://example.com">外部リンク</Link>);
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("target", "_blank");
    expect(link).toHaveAttribute("rel", "noopener noreferrer");
  });

  it("内部リンク（相対パス）の場合は target が設定されない", () => {
    render(<Link href="/internal">内部リンク</Link>);
    const link = screen.getByRole("link");
    expect(link).not.toHaveAttribute("target");
  });

  it("ハッシュリンクの場合は target が設定されない", () => {
    render(<Link href="#section">ハッシュリンク</Link>);
    const link = screen.getByRole("link");
    expect(link).not.toHaveAttribute("target");
  });

  it("同一オリジンのフルURLは外部リンクとして扱われない", () => {
    const origin = window.location.origin;
    render(<Link href={`${origin}/page`}>同一オリジン</Link>);
    const link = screen.getByRole("link");
    expect(link).not.toHaveAttribute("target", "_blank");
  });
});
