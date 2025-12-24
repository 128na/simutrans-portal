/**
 * フォーム送信の二重送信を防止する
 *
 * 使い方:
 * <form class="js-submit-once" data-loading-text="ログイン中...">
 *   <button type="submit">送信</button>
 * </form>
 *
 * data-loading-text属性を省略した場合は「送信中...」が表示されます
 */
document
  .querySelectorAll<HTMLFormElement>("form.js-submit-once")
  .forEach((form) => {
    form.addEventListener("submit", (ev) => {
      const target = ev.target as HTMLFormElement;

      // 既に送信済みの場合は送信をキャンセル
      if (target.dataset.submitting === "true") {
        ev.preventDefault();
        return;
      }

      // 送信中フラグを立てる
      target.dataset.submitting = "true";

      // ローディングテキスト（data-loading-text属性で指定可能、デフォルトは「送信中...」）
      const loadingText = target.dataset.loadingText || "送信中...";

      // フォーム内のすべての送信ボタンを無効化してローディング表示
      const submitButtons = target.querySelectorAll<
        HTMLButtonElement | HTMLInputElement
      >('button[type="submit"], input[type="submit"]');
      submitButtons.forEach((button) => {
        button.disabled = true;

        // ボタンの場合、元のHTMLを保存してローディングアイコンに置き換え
        if (button instanceof HTMLButtonElement) {
          button.dataset.originalHtml = button.innerHTML;
          button.innerHTML = `
            <svg class="animate-spin h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">${loadingText}</span>
          `;
        }
      });
    });
  });
