/// <reference types="vite/client" />

interface ImportMetaEnv {
  // 必須環境変数
  readonly VITE_API_URL: string;

  // オプション環境変数
  readonly VITE_APP_URL?: string;
  readonly VITE_GOOGLE_RECAPTCHA_SITE_KEY?: string;
  readonly VITE_ONESIGNAL_APP_ID?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
