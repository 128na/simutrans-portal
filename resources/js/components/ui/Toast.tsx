import { useEffect } from "react";
import type { Toast as ToastType } from "@/contexts/ToastContext";

interface ToastProps {
  toast: ToastType;
  onDismiss: (id: string) => void;
}

/**
 * トースト通知コンポーネント
 *
 * @example
 * ```tsx
 * <Toast toast={{ id: '1', message: '成功', variant: 'success', duration: 3000 }} onDismiss={() => {}} />
 * ```
 */
const Toast = ({ toast, onDismiss }: ToastProps) => {
  // バリアント別のスタイル
  const variantConfig = {
    success: {
      bg: "bg-green-50",
      border: "border-green-300",
      text: "text-green-800",
      icon: "✓",
      label: "成功",
    },
    error: {
      bg: "bg-red-50",
      border: "border-red-300",
      text: "text-red-800",
      icon: "✕",
      label: "エラー",
    },
    warning: {
      bg: "bg-yellow-50",
      border: "border-yellow-300",
      text: "text-yellow-800",
      icon: "⚠",
      label: "警告",
    },
    info: {
      bg: "bg-blue-50",
      border: "border-blue-300",
      text: "text-blue-800",
      icon: "ℹ",
      label: "情報",
    },
  };

  const config = variantConfig[toast.variant];

  useEffect(() => {
    const timer = setTimeout(() => {
      onDismiss(toast.id);
    }, toast.duration);

    return () => clearTimeout(timer);
  }, [toast.id, toast.duration, onDismiss]);

  return (
    <div
      className={`
        ${config.bg} ${config.border} ${config.text}
        border rounded-lg shadow-lg px-4 py-3 flex gap-3 items-start pointer-events-auto
        animate-in fade-in slide-in-from-bottom-2 duration-200
        animate-out fade-out slide-out-to-right-2 duration-200
        max-w-sm sm:max-w-md w-full
      `}
      role="alert"
      aria-label={config.label}
    >
      {/* アイコン */}
      <div className="shrink-0 font-bold text-lg mt-0.5">{config.icon}</div>

      {/* メッセージ */}
      <div className="flex-1 min-w-0">
        <p className="text-sm font-medium break-words">{toast.message}</p>
      </div>

      {/* 閉じるボタン */}
      <button
        onClick={() => onDismiss(toast.id)}
        className="shrink-0 ml-2 hover:opacity-70 transition-opacity"
        aria-label="閉じる"
        type="button"
      >
        <span className="text-lg">✕</span>
      </button>
    </div>
  );
};

export default Toast;
