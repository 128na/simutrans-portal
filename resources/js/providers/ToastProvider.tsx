import { useState, useCallback, ReactNode } from "react";
import { ToastContext, type Toast } from "@/contexts/ToastContext";

interface ToastProviderProps {
  children: ReactNode;
  maxToasts?: number;
}

/**
 * トースト通知プロバイダー
 * アプリケーションのルートにラップして使用
 *
 * @example
 * ```tsx
 * <ToastProvider maxToasts={5}>
 *   <App />
 * </ToastProvider>
 * ```
 */
export const ToastProvider = ({
  children,
  maxToasts = 5,
}: ToastProviderProps) => {
  const [toasts, setToasts] = useState<Toast[]>([]);
  // 重複制御用: メッセージごとの最後の表示時刻
  const [lastShownTime, setLastShownTime] = useState<Record<string, number>>(
    {}
  );

  const dismiss = useCallback((id: string) => {
    setToasts((prev) => prev.filter((toast) => toast.id !== id));
  }, []);

  const show = useCallback(
    (
      message: string,
      variant: "success" | "error" | "warning" | "info",
      duration: number
    ): string => {
      const now = Date.now();
      const lastTime = lastShownTime[message] ?? 0;

      // 同じメッセージは5秒以内は無視
      if (now - lastTime < 5000) {
        return "";
      }

      const id = `toast-${now}-${Math.random()}`;

      setToasts((prev) => {
        const updated = [
          ...prev,
          { id, message, variant, duration, createdAt: now },
        ];
        // 最大件数を超えたら古いものから削除
        return updated.slice(-maxToasts);
      });

      setLastShownTime((prev) => ({ ...prev, [message]: now }));

      // 自動削除
      setTimeout(() => {
        dismiss(id);
      }, duration);

      return id;
    },
    [lastShownTime, maxToasts, dismiss]
  );
  const dismissAll = useCallback(() => {
    setToasts([]);
  }, []);

  return (
    <ToastContext.Provider value={{ toasts, show, dismiss, dismissAll }}>
      {children}
    </ToastContext.Provider>
  );
};
