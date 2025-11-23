const siteKey = import.meta.env.VITE_GOOGLE_RECAPTCHA_SITE_KEY;
const app = document.getElementById("inviteDiscord");
const form = document.getElementById("inviteForm");
const recaptchaToken = document.getElementById("recaptchaToken");

if (siteKey && app && recaptchaToken && form) {
  app.addEventListener("click", async (ev) => {
    ev.preventDefault();

    try {
      // reCAPTCHAトークンを取得
      const token = await window.grecaptcha.enterprise.execute(siteKey, {
        action: "invite",
      });
      recaptchaToken.value = token;

      // トークンをセットしたあとに送信
      form.submit();
    } catch (err) {
      console.error("reCAPTCHA エラー:", err);
      alert("reCAPTCHAの認証に失敗しました。もう一度お試しください。");
    }
  });
}
