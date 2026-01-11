import { useContext } from "react";
import { ToastContext } from "@/contexts/ToastContext";
import Toast from "./Toast";

/**
 * トースト通知コンテナー
 * 複数のトーストを管理・表示する
 *
 * ToastProviderの中で自動的に使用されるため、
 * 通常は直接使用する必要はない
 */
export const ToastContainer = () => {
  const context = useContext(ToastContext);

  if (context === undefined) {
    return null;
  }

  const { toasts, dismiss } = context;

  if (toasts.length === 0) {
    return null;
  }

  return (
    <div
      className="fixed inset-0 pointer-events-none flex flex-col items-end justify-end gap-2 p-4 z-[60]"
      role="region"
      aria-live="polite"
      aria-label="通知"
    >
      {/* スマートフォン対応: 小画面では中央揃え */}
      <div className="w-full sm:w-auto flex flex-col gap-2 items-stretch sm:items-end">
        {toasts.map((toast) => (
          <Toast key={toast.id} toast={toast} onDismiss={dismiss} />
        ))}
      </div>
    </div>
  );
};

export default ToastContainer;
