import { useContext } from "react";
import { ToastContext } from "@/contexts/ToastContext";

export interface ToastOptions {
  duration?: number; // ミリ秒、デフォルト3000
}

/**
 * トースト通知フック
 *
 * @example
 * ```tsx
 * const { showSuccess, showError } = useToast();
 *
 * showSuccess("保存しました");
 * showError("エラーが発生しました");
 * ```
 */
export const useToast = () => {
  const context = useContext(ToastContext);

  if (context === undefined) {
    throw new Error("useToast must be used within ToastProvider");
  }

  const { show, dismiss, dismissAll } = context;

  return {
    /**
     * 成功トースト表示
     */
    showSuccess: (message: string, options?: ToastOptions) =>
      show(message, "success", options?.duration ?? 3000),

    /**
     * エラートースト表示
     */
    showError: (message: string, options?: ToastOptions) =>
      show(message, "error", options?.duration ?? 3000),

    /**
     * 警告トースト表示
     */
    showWarning: (message: string, options?: ToastOptions) =>
      show(message, "warning", options?.duration ?? 3000),

    /**
     * 情報トースト表示
     */
    showInfo: (message: string, options?: ToastOptions) =>
      show(message, "info", options?.duration ?? 3000),

    /**
     * トースト閉じる
     */
    dismiss,

    /**
     * 全トースト閉じる
     */
    dismissAll,
  };
};
