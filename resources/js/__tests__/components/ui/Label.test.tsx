import Label from "@/components/ui/Label";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("Label コンポーネント", () => {
  it("基本的なレンダリング", () => {
    render(<Label>ラベルテキスト</Label>);
    expect(screen.getByText("ラベルテキスト")).toBeInTheDocument();
  });

  it("子要素がレンダリングされる", () => {
    render(
      <Label>
        <span>子要素</span>
      </Label>
    );
    expect(screen.getByText("子要素")).toBeInTheDocument();
  });

  it("カスタムクラス名が適用される", () => {
    render(<Label className="custom-class">ラベル</Label>);
    const label = screen.getByText("ラベル");
    expect(label).toHaveClass("custom-class");
  });

  it("デフォルトのクラス名が含まれる", () => {
    render(<Label>ラベル</Label>);
    const label = screen.getByText("ラベル");
    expect(label).toHaveClass("block");
    expect(label).toHaveClass("text-sm");
  });

  it("htmlFor 属性が設定される", () => {
    render(<Label htmlFor="test-input">入力フィールド</Label>);
    const label = screen.getByText("入力フィールド");
    expect(label).toHaveAttribute("for", "test-input");
  });
});
