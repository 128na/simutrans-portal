/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_API_URL: string;
  readonly VITE_APP_URL: string;
  readonly VITE_APP_NAME: string;
  readonly VITE_GOOGLE_RECAPTCHA_SITE_KEY?: string;
  readonly VITE_ONESIGNAL_APP_ID?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
