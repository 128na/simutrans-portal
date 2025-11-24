/**
 * 型安全な環境変数アクセスを提供
 *
 * import.meta.env を直接使用せず、このモジュールを経由して環境変数にアクセスすることで
 * 型安全性を確保し、IDEの補完が効くようになります。
 */

/**
 * 環境変数の型定義
 */
interface EnvironmentVariables {
  readonly apiUrl: string;
  readonly appUrl: string;
  readonly appName: string;
  readonly recaptchaSiteKey: string | undefined;
  readonly onesignalAppId: string | undefined;
  readonly isDevelopment: boolean;
}

/**
 * 必須環境変数のバリデーション
 * アプリケーション起動時に必須環境変数が設定されているかチェック
 */
function validateRequiredEnv(): void {
  const required: Array<keyof ImportMetaEnv> = [
    "VITE_API_URL",
    "VITE_APP_URL",
    "VITE_APP_NAME",
  ];

  const missing: string[] = [];

  for (const key of required) {
    if (!import.meta.env[key]) {
      missing.push(key);
    }
  }

  if (missing.length > 0) {
    throw new Error(
      `Missing required environment variables: ${missing.join(", ")}\n` +
        "Please check your .env file and ensure all required variables are set."
    );
  }
}

/**
 * 型安全な環境変数アクセサー
 *
 * @example
 * import { env } from '@/lib/env';
 *
 * // 型安全にアクセス（string型として保証）
 * const apiUrl = env.apiUrl;
 *
 * // オプション値のチェック
 * if (env.recaptchaSiteKey) {
 *   // reCAPTCHAが有効
 * }
 */
export const env: EnvironmentVariables = {
  apiUrl: import.meta.env.VITE_API_URL,
  appUrl: import.meta.env.VITE_APP_URL,
  appName: import.meta.env.VITE_APP_NAME,
  recaptchaSiteKey: import.meta.env.VITE_GOOGLE_RECAPTCHA_SITE_KEY || undefined,
  onesignalAppId: import.meta.env.VITE_ONESIGNAL_APP_ID || undefined,
  isDevelopment: import.meta.env.DEV,
};

// アプリケーション起動時に必須環境変数をバリデーション
validateRequiredEnv();
