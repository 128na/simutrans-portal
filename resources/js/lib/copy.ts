document.querySelectorAll(".js-clipboard").forEach((el) =>
  el.addEventListener("click", async (ev) => {
    ev.preventDefault();
    try {
      const el = ev.target as HTMLElement;
      await navigator.clipboard.writeText(el.dataset.text ?? "");
      alert("クリップボードにコピーしました");
    } catch (error) {
      console.error(error);
    }
  })
);
