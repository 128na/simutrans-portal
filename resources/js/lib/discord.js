import { env, hasGoogleRecaptcha } from "./env";

const app = document.getElementById("inviteDiscord");
const form = document.getElementById("inviteForm");
const recaptchaToken = document.getElementById("recaptchaToken");

if (hasGoogleRecaptcha() && app && recaptchaToken && form) {
  app.addEventListener("click", async (ev) => {
    ev.preventDefault();

    try {
      // reCAPTCHAトークンを取得
      const token = await window.grecaptcha.enterprise.execute(
        env.googleRecaptchaSiteKey,
        {
          action: "invite",
        }
      );
      recaptchaToken.value = token;

      // トークンをセットしたあとに送信
      form.submit();
    } catch {
      alert("reCAPTCHAの認証に失敗しました。もう一度お試しください。");
    }
  });
}
