import TextError from "@/components/ui/TextError";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("TextError コンポーネント", () => {
  it("エラーメッセージが表示される", () => {
    render(<TextError>エラーが発生しました</TextError>);
    expect(screen.getByText("エラーが発生しました")).toBeInTheDocument();
  });

  it("空文字列の場合は何も表示しない", () => {
    const { container } = render(<TextError>{""}</TextError>);
    expect(container.firstChild).toBeNull();
  });

  it("カスタムクラス名が適用される", () => {
    render(<TextError className="custom-class">エラー</TextError>);
    const error = screen.getByText("エラー");
    expect(error).toHaveClass("custom-class");
  });

  it("デフォルトのスタイルが適用される", () => {
    render(<TextError>エラー</TextError>);
    const error = screen.getByText("エラー");
    expect(error).toHaveClass("text-c-danger");
    expect(error).toHaveClass("text-sm");
  });

  it("複数行のエラーメッセージが表示される", () => {
    render(
      <TextError>
        エラー1
        <br />
        エラー2
      </TextError>
    );
    expect(screen.getByText(/エラー1/)).toBeInTheDocument();
  });
});
