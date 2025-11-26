import { render, screen } from "@testing-library/react";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import { ErrorBoundary } from "@/components/ErrorBoundary";

// logger モジュールをモック
vi.mock("@/utils/logger", () => ({
  logger: {
    debug: vi.fn(),
    error: vi.fn(),
    warn: vi.fn(),
  },
}));

// エラーを投げるテスト用コンポーネント
const ThrowErrorComponent = ({
  shouldThrow = true,
}: {
  shouldThrow?: boolean;
}) => {
  if (shouldThrow) {
    throw new Error("テストエラー");
  }
  return <div>正常表示</div>;
};

describe("ErrorBoundary", () => {
  let alertMock: ReturnType<typeof vi.fn>;
  let consoleErrorSpy: ReturnType<typeof vi.spyOn>;

  beforeEach(() => {
    vi.clearAllMocks();
    alertMock = vi.fn();
    vi.stubGlobal("alert", alertMock);
    // React のエラーログを抑制
    consoleErrorSpy = vi.spyOn(console, "error").mockImplementation(() => {});
  });

  afterEach(() => {
    consoleErrorSpy.mockRestore();
  });

  it("正常時は子コンポーネントをレンダリングする", () => {
    render(
      <ErrorBoundary>
        <div>テストコンテンツ</div>
      </ErrorBoundary>
    );

    expect(screen.getByText("テストコンテンツ")).toBeInTheDocument();
  });

  it("エラー時はデフォルトのフォールバックUIを表示する", () => {
    render(
      <ErrorBoundary>
        <ThrowErrorComponent />
      </ErrorBoundary>
    );

    expect(screen.getByText("エラーが発生しました")).toBeInTheDocument();
    expect(
      screen.getByText(
        "ページを再読み込みしてください。問題が続く場合はお問い合わせください。"
      )
    ).toBeInTheDocument();
  });

  it("カスタムフォールバックUIを表示できる", () => {
    render(
      <ErrorBoundary fallback={<div>カスタムエラー</div>}>
        <ThrowErrorComponent />
      </ErrorBoundary>
    );

    expect(screen.getByText("カスタムエラー")).toBeInTheDocument();
  });

  it("エラー発生時にユーザーへのalertは表示しない（サイレント）", () => {
    render(
      <ErrorBoundary name="TestBoundary">
        <ThrowErrorComponent />
      </ErrorBoundary>
    );

    // ErrorBoundaryはサイレントモードで処理するため、alertは表示されない
    expect(alertMock).not.toHaveBeenCalled();
  });

  it("nameプロパティを設定できる", () => {
    render(
      <ErrorBoundary name="CustomBoundary">
        <ThrowErrorComponent />
      </ErrorBoundary>
    );

    // エラーが発生してフォールバックが表示されることを確認
    expect(screen.getByText("エラーが発生しました")).toBeInTheDocument();
  });
});
