/**
 * クリップボードにテキストをコピーする
 *
 * 【推奨】この関数でのメッセージ表示は非推奨です。
 * コンポーネント内で useToast() フックの showSuccess/showError を
 * 直接使用する方法が推奨されます。
 *
 * @example
 * ```typescript
 * // 推奨される使用方法
 * const { showSuccess, showError } = useToast();
 *
 * if (await copyToClipboard(url)) {
 *   showSuccess("コピーしました");
 * } else {
 *   showError("コピーに失敗しました");
 * }
 *
 * // 従来の使用方法（非推奨、互換性のため残存）
 * await copyToClipboard(text, "コピーしました", "コピーに失敗しました");
 * ```
 *
 * @param text コピー対象のテキスト
 * @param successMessage 成功時のメッセージ（指定時はalertで表示、非推奨）
 * @param errorMessage 失敗時のメッセージ（指定時はalertで表示、非推奨）
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
 * @deprecated
 * この関数は非推奨です。useToast() フックの showSuccess/showError を
 * 直接使用してください。本関数は限定的な互換性のため残存しています。
 *
 * @example
 * ```typescript
 * const { showSuccess } = useToast();
 * showSuccess(message);  // 推奨される用法
 * ```
 *
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
