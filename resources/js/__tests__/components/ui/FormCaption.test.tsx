import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { FormCaption } from "@/components/ui/FormCaption";

describe("FormCaption", () => {
  it("キャプションテキストが表示される", () => {
    render(<FormCaption>フォームのキャプション</FormCaption>);
    expect(screen.getByText("フォームのキャプション")).toBeInTheDocument();
  });

  it("v2-text-captionクラスが適用される", () => {
    render(<FormCaption>キャプション</FormCaption>);
    const caption = screen.getByText("キャプション");
    expect(caption).toHaveClass("v2-form-caption", "mb-2");
  });

  it("複数の子要素を表示できる", () => {
    render(
      <FormCaption>
        <span>セクション1</span>
        <span>セクション2</span>
      </FormCaption>
    );
    expect(screen.getByText("セクション1")).toBeInTheDocument();
    expect(screen.getByText("セクション2")).toBeInTheDocument();
  });

  it("HTML属性を渡せる", () => {
    render(
      <FormCaption data-testid="caption" id="form-caption">
        キャプション
      </FormCaption>
    );
    const caption = screen.getByTestId("caption");
    expect(caption).toHaveAttribute("id", "form-caption");
  });

  it("ReactNodeを子要素として受け取れる", () => {
    render(
      <FormCaption>
        <strong>重要</strong>なキャプション
      </FormCaption>
    );
    expect(screen.getByText("重要")).toBeInTheDocument();
    expect(screen.getByText("なキャプション")).toBeInTheDocument();
  });
});
