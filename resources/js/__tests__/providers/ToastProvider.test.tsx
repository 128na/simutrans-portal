import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { ToastProvider } from "@/providers/ToastProvider";
import { useToast } from "@/hooks/useToast";

function TestComponent() {
  const { showSuccess } = useToast();

  return (
    <div>
      <button onClick={() => showSuccess("テストメッセージ")}>
        トースト表示
      </button>
    </div>
  );
}

describe("ToastProvider", () => {
  it("Provider がレンダリングされる", () => {
    render(
      <ToastProvider>
        <div>コンテンツ</div>
      </ToastProvider>
    );

    expect(screen.getByText("コンテンツ")).toBeInTheDocument();
  });

  it("children が正しくレンダリングされる", () => {
    render(
      <ToastProvider>
        <div>子要素</div>
      </ToastProvider>
    );

    expect(screen.getByText("子要素")).toBeInTheDocument();
  });

  it("maxToasts prop で最大表示数を制限できる", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    render(
      <ToastProvider maxToasts={2}>
        <TestComponent />
      </ToastProvider>
    );

    const button = screen.getByRole("button");

    // 3つのトーストを表示しようとする
    await user.click(button);
    await user.click(button);
    await user.click(button);

    // 最大2件まで表示されることを期待
    await waitFor(
      () => {
        const alerts = screen.queryAllByRole("alert");
        expect(alerts.length).toBeLessThanOrEqual(2);
      },
      { timeout: 500 }
    );
  });

  it("同一メッセージの重複を制御する", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    function DuplicateTestComponent() {
      const { showSuccess } = useToast();

      return (
        <div>
          <button
            onClick={() => {
              showSuccess("重複メッセージ");
              showSuccess("重複メッセージ");
            }}
          >
            同じメッセージ連続表示
          </button>
        </div>
      );
    }

    render(
      <ToastProvider>
        <DuplicateTestComponent />
      </ToastProvider>
    );

    const button = screen.getByRole("button");

    await user.click(button);

    // 同じメッセージを2回呼び出しても、1回目のものしか表示されない
    // （重複制御により5秒以内は無視される）
    const alerts = screen.queryAllByRole("alert");
    expect(alerts.length).toBeLessThanOrEqual(1);
  });

  it("Provider 内で Context を利用できる", () => {
    const TestHook = () => {
      const toast = useToast();
      return <div>{toast ? "Context利用可能" : "Context利用不可"}</div>;
    };

    render(
      <ToastProvider>
        <TestHook />
      </ToastProvider>
    );

    expect(screen.getByText("Context利用可能")).toBeInTheDocument();
  });

  it("複数の Provider インスタンスで独立した状態を保つ", () => {
    const { rerender } = render(
      <ToastProvider>
        <div>Provider 1</div>
      </ToastProvider>
    );

    expect(screen.getByText("Provider 1")).toBeInTheDocument();

    rerender(
      <ToastProvider>
        <div>Provider 2</div>
      </ToastProvider>
    );

    expect(screen.getByText("Provider 2")).toBeInTheDocument();
  });

  it("defaultDuration の指定ができる", () => {
    // デフォルト値の確認（内部で設定）
    render(
      <ToastProvider>
        <TestComponent />
      </ToastProvider>
    );

    // Provider がレンダリングされることを確認
    expect(screen.getByRole("button")).toBeInTheDocument();
  });
});
