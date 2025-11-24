document.querySelectorAll("form.js-confirm").forEach((el) =>
  el.addEventListener("submit", (ev) => {
    ev.preventDefault();
    try {
      const form = ev.target as HTMLFormElement;
      if (window.confirm(form.dataset.text ?? "実行しますか？")) {
        form.submit();
      }
    } catch {
      // フォーム送信のエラーは無視（通常発生しない）
    }
  })
);
