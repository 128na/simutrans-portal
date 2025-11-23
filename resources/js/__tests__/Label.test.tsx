import { describe, it, expect } from "vitest";
import { render, screen } from "@testing-library/react";
import Label from "../apps/components/ui/Label";

describe("Label", () => {
  it("基本的なレンダリング", () => {
    render(<Label>テストラベル</Label>);
    expect(screen.getByText("テストラベル")).toBeInTheDocument();
  });

  it("childrenが正しく表示される", () => {
    render(<Label>ユーザー名</Label>);
    expect(screen.getByText("ユーザー名")).toBeInTheDocument();
  });

  it("カスタムclassNameが適用される", () => {
    render(<Label className="custom-label">Label</Label>);
    expect(screen.getByText("Label")).toHaveClass("custom-label");
  });

  it("htmlFor属性が正しく設定される", () => {
    render(<Label htmlFor="test-input">Test Label</Label>);
    const label = screen.getByText("Test Label");
    expect(label).toHaveAttribute("for", "test-input");
  });

  it("複数の子要素を持つことができる", () => {
    render(
      <Label>
        <span>Label: </span>
        <strong>必須</strong>
      </Label>
    );
    expect(screen.getByText("Label:")).toBeInTheDocument();
    expect(screen.getByText("必須")).toBeInTheDocument();
  });

  it("デフォルトのスタイルクラスが適用される", () => {
    render(<Label>Test</Label>);
    const label = screen.getByText("Test");
    expect(label).toHaveClass("block");
    expect(label).toHaveClass("text-sm");
    expect(label).toHaveClass("text-gray-900");
  });
});
