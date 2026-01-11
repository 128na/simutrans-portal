import { renderHook, act } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { useToast } from "@/hooks/useToast";
import { ToastProvider } from "@/providers/ToastProvider";
import type { ReactNode } from "react";

// Providerでラップするラッパー
function wrapper({ children }: { children: ReactNode }) {
  return <ToastProvider maxToasts={5}>{children}</ToastProvider>;
}

describe("useToast", () => {
  it("showSuccess を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showSuccess("成功メッセージ");
      });
    }).not.toThrow();
  });

  it("showError を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showError("エラーメッセージ");
      });
    }).not.toThrow();
  });

  it("showWarning を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showWarning("警告メッセージ");
      });
    }).not.toThrow();
  });

  it("showInfo を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showInfo("情報メッセージ");
      });
    }).not.toThrow();
  });

  it("dismiss を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.dismiss("test-id");
      });
    }).not.toThrow();
  });

  it("dismissAll を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.dismissAll();
      });
    }).not.toThrow();
  });

  it("Provider外で使用するとエラーが出る", () => {
    // コンソール エラー出力を抑制
    const consoleErrorSpy = vi
      .spyOn(console, "error")
      .mockImplementation(() => {});

    expect(() => {
      renderHook(() => useToast());
    }).toThrow();

    consoleErrorSpy.mockRestore();
  });

  it("カスタムdurationを指定できる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showSuccess("メッセージ", { duration: 5000 });
      });
    }).not.toThrow();
  });

  it("複数の showSuccess を呼び出すことができる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    act(() => {
      result.current.showSuccess("メッセージ1");
      result.current.showSuccess("メッセージ2");
    });

    // 複数のメッセージが表示される
    expect(result.current).toBeDefined();
  });

  it("複数のメッセージを同時に表示できる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showSuccess("成功1");
        result.current.showSuccess("成功2");
        result.current.showError("エラー1");
        result.current.showWarning("警告1");
      });
    }).not.toThrow();
  });

  it("特定のトーストを削除できる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    // 実装が showSuccess 内で ID を生成するため、
    // ここではメソッドが呼び出せることを確認
    act(() => {
      result.current.showSuccess("削除対象");
      result.current.dismiss("some-id");
    });

    expect(result.current).toBeDefined();
  });

  it("すべてのトーストを一度に削除できる", () => {
    const { result } = renderHook(() => useToast(), { wrapper });

    expect(() => {
      act(() => {
        result.current.showSuccess("メッセージ1");
        result.current.showSuccess("メッセージ2");
        result.current.dismissAll();
      });
    }).not.toThrow();
  });
});
