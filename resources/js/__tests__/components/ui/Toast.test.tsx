import Toast from "@/components/ui/Toast";
import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import type { Toast as ToastType } from "@/contexts/ToastContext";

describe("Toast コンポーネント", () => {
  const createMockToast = (overrides?: Partial<ToastType>): ToastType => ({
    id: "test-1",
    message: "テストメッセージ",
    variant: "success",
    duration: 3000,
    createdAt: Date.now(),
    ...overrides,
  });

  const mockDismiss = vi.fn();

  it("成功トーストが表示される", () => {
    const toast = createMockToast({ variant: "success" });
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    expect(screen.getByText("テストメッセージ")).toBeInTheDocument();
    expect(screen.getByRole("alert")).toHaveClass("v2-card-success");
  });

  it("エラートーストが表示される", () => {
    const toast = createMockToast({ variant: "error" });
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    expect(screen.getByText("テストメッセージ")).toBeInTheDocument();
    expect(screen.getByRole("alert")).toHaveClass("v2-card-danger");
  });

  it("警告トーストが表示される", () => {
    const toast = createMockToast({ variant: "warning" });
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    expect(screen.getByText("テストメッセージ")).toBeInTheDocument();
    expect(screen.getByRole("alert")).toHaveClass("v2-card-warning");
  });

  it("情報トーストが表示される", () => {
    const toast = createMockToast({ variant: "info" });
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    expect(screen.getByText("テストメッセージ")).toBeInTheDocument();
    expect(screen.getByRole("alert")).toHaveClass("v2-card-info");
  });

  it("閉じるボタンをクリックすると onDismiss が呼ばれる", async () => {
    const user = userEvent.setup();
    const toast = createMockToast();
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    const closeButton = screen.getByRole("button");
    await user.click(closeButton);

    expect(mockDismiss).toHaveBeenCalledWith("test-1");
  });

  it("duration 経過後に自動的に onDismiss が呼ばれる", async () => {
    const toast = createMockToast({ duration: 100 });
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    await waitFor(
      () => {
        expect(mockDismiss).toHaveBeenCalledWith("test-1");
      },
      { timeout: 500 }
    );
  });

  it("アイコンが正しく表示される", () => {
    const { rerender } = render(
      <Toast
        toast={createMockToast({ variant: "success" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByText("✓")).toBeInTheDocument();

    rerender(
      <Toast
        toast={createMockToast({ variant: "error" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByText("✕")).toBeInTheDocument();

    rerender(
      <Toast
        toast={createMockToast({ variant: "warning" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByText("⚠")).toBeInTheDocument();

    rerender(
      <Toast
        toast={createMockToast({ variant: "info" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByText("ℹ")).toBeInTheDocument();
  });

  it("スタイルが variant に応じて変更される", () => {
    const { rerender } = render(
      <Toast
        toast={createMockToast({ variant: "success" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByRole("alert")).toHaveClass("v2-card-success");

    rerender(
      <Toast
        toast={createMockToast({ variant: "error" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByRole("alert")).toHaveClass("v2-card-danger");

    rerender(
      <Toast
        toast={createMockToast({ variant: "warning" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByRole("alert")).toHaveClass("v2-card-warning");

    rerender(
      <Toast
        toast={createMockToast({ variant: "info" })}
        onDismiss={mockDismiss}
      />
    );
    expect(screen.getByRole("alert")).toHaveClass("v2-card-info");
  });

  it("複数行メッセージが表示される", () => {
    const longMessage = "これは\n複数行の\nテストメッセージです";
    const toast = createMockToast({ message: longMessage });
    render(<Toast toast={toast} onDismiss={mockDismiss} />);

    expect(screen.getByText(longMessage)).toBeInTheDocument();
  });

  it("アニメーション クラスが適用される", () => {
    const toast = createMockToast();
    const { container } = render(
      <Toast toast={toast} onDismiss={mockDismiss} />
    );

    const toastElement = container.firstChild as HTMLElement;
    expect(toastElement).toHaveClass("animate-in");
    expect(toastElement).toHaveClass("fade-in");
    expect(toastElement).toHaveClass("slide-in-from-bottom-5");
  });
});
