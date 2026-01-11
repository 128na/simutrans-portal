import ToastContainer from "@/components/ui/ToastContainer";
import { ToastProvider } from "@/providers/ToastProvider";
import { useToast } from "@/hooks/useToast";
import { render, screen, waitFor, fireEvent } from "@testing-library/react";
import { describe, expect, it } from "vitest";

// useToast を使用するテストコンポーネント
function TestComponent() {
  const { showSuccess, showError, showWarning, showInfo } = useToast();

  return (
    <div>
      <button onClick={() => showSuccess("成功メッセージ")}>成功を表示</button>
      <button onClick={() => showError("エラーメッセージ")}>
        エラーを表示
      </button>
      <button onClick={() => showWarning("警告メッセージ")}>警告を表示</button>
      <button onClick={() => showInfo("情報メッセージ")}>情報を表示</button>
    </div>
  );
}

describe("ToastContainer", () => {
  it("トーストがある場合、コンテナが存在する", () => {
    render(
      <ToastProvider>
        <TestComponent />
        <ToastContainer />
      </ToastProvider>
    );

    // ボタンをクリックしてトースト表示
    const button = screen.getByRole("button", { name: "成功を表示" });
    fireEvent.click(button);

    expect(screen.getByRole("region")).toBeInTheDocument();
  });

  it("aria-live が polite に設定されている", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    render(
      <ToastProvider>
        <TestComponent />
        <ToastContainer />
      </ToastProvider>
    );

    const successButton = screen.getByRole("button", {
      name: "成功を表示",
    });
    await user.click(successButton);

    await waitFor(() => {
      const region = screen.getByRole("region");
      expect(region).toHaveAttribute("aria-live", "polite");
    });
  });

  it("複数のトーストが表示される", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    render(
      <ToastProvider maxToasts={5}>
        <TestComponent />
        <ToastContainer />
      </ToastProvider>
    );

    const successButton = screen.getByRole("button", {
      name: "成功を表示",
    });
    const errorButton = screen.getByRole("button", {
      name: "エラーを表示",
    });

    await user.click(successButton);
    await user.click(errorButton);

    await waitFor(() => {
      expect(screen.getByText("成功メッセージ")).toBeInTheDocument();
      expect(screen.getByText("エラーメッセージ")).toBeInTheDocument();
    });
  });

  it("トーストが最大件数まで表示される", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    render(
      <ToastProvider maxToasts={2}>
        <TestComponent />
        <ToastContainer />
      </ToastProvider>
    );

    const successButton = screen.getByRole("button", {
      name: "成功を表示",
    });

    // 3つのトーストを表示しようとする
    await user.click(successButton);
    await user.click(successButton);
    await user.click(successButton);

    // 最大2件まで表示される
    await waitFor(() => {
      const toasts = screen.getAllByRole("alert");
      expect(toasts.length).toBeLessThanOrEqual(2);
    });
  });

  it("トーストコンテナが正しいクラスを持つ", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    render(
      <ToastProvider>
        <TestComponent />
        <ToastContainer />
      </ToastProvider>
    );

    const button = screen.getByRole("button", { name: "成功を表示" });
    await user.click(button);

    await waitFor(() => {
      const region = screen.getByRole("region");
      expect(region).toHaveClass("fixed");
      expect(region).toHaveClass("z-[60]");
    });
  });

  it("レスポンシブレイアウトが適用される", async () => {
    const user = await import("@testing-library/user-event").then((m) =>
      m.default.setup()
    );

    render(
      <ToastProvider>
        <TestComponent />
        <ToastContainer />
      </ToastProvider>
    );

    const button = screen.getByRole("button", { name: "成功を表示" });
    await user.click(button);

    await waitFor(() => {
      const region = screen.getByRole("region");

      // レスポンシブクラスが含まれている
      expect(region).toHaveClass("inset-0");
      expect(region).toHaveClass("pointer-events-none");
    });
  });
});
