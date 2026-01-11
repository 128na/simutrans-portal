import ToastContainer from "@/components/ui/ToastContainer";
import { ToastProvider } from "@/providers/ToastProvider";
import { useToast } from "@/hooks/useToast";
import { render, screen, waitFor } from "@testing-library/react";
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
  it("トーストコンテナが存在する", () => {
    render(
      <ToastProvider>
        <ToastContainer />
      </ToastProvider>
    );

    expect(screen.getByRole("region")).toBeInTheDocument();
  });

  it("aria-live が polite に設定されている", () => {
    render(
      <ToastProvider>
        <ToastContainer />
      </ToastProvider>
    );

    const region = screen.getByRole("region");
    expect(region).toHaveAttribute("aria-live", "polite");
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

  it("トーストコンテナが正しいクラスを持つ", () => {
    const { container } = render(
      <ToastProvider>
        <ToastContainer />
      </ToastProvider>
    );

    const region = container.querySelector("[role='region']") as HTMLElement;
    expect(region).toHaveClass("fixed");
    expect(region).toHaveClass("z-[60]");
  });

  it("レスポンシブレイアウトが適用される", () => {
    const { container } = render(
      <ToastProvider>
        <ToastContainer />
      </ToastProvider>
    );

    const region = container.querySelector("[role='region']") as HTMLElement;

    // レスポンシブクラスが含まれている
    expect(region).toHaveClass("bottom-4");
    expect(region).toHaveClass("right-4");
  });
});
