/**
 * クリップボードにテキストをコピーする
 * @param text コピー対象のテキスト
 * @param successMessage 成功時のメッセージ（指定時はalertで表示）
 * @param errorMessage 失敗時のメッセージ（指定時はalertで表示）
 * @returns 成功時true、失敗時false
 */
export const copyToClipboard = async (
  text: string,
  successMessage?: string,
  errorMessage?: string
): Promise<boolean> => {
  try {
    await navigator.clipboard.writeText(text);
    if (successMessage) {
      alert(successMessage);
    }
    return true;
  } catch {
    if (errorMessage) {
      alert(errorMessage);
    }
    return false;
  }
};

/**
 * トースト通知を表示する（簡易版）
 * @param message 表示するメッセージ
 * @param duration 表示時間（ミリ秒）
 */
export const showToast = (message: string, duration: number = 3000): void => {
  const toast = document.createElement("div");
  toast.className =
    "fixed bottom-4 right-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded z-50";
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.remove();
  }, duration);
};
