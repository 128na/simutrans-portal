/**
 * 型安全な環境変数アクセス
 *
 * 環境変数をビルド時にチェックし、実行時エラーを防ぐ
 */

/**
 * 必須環境変数のバリデーション
 * アプリケーション起動時に必須の環境変数が存在するかチェック
 */
function validateRequiredEnv(): void {
  const required = ["VITE_API_URL"] as const;

  for (const key of required) {
    if (!import.meta.env[key]) {
      throw new Error(`Missing required environment variable: ${key}`);
    }
  }
}

// アプリケーション起動時にバリデーション実行
validateRequiredEnv();

/**
 * 型安全な環境変数オブジェクト
 * すべての環境変数アクセスをこのオブジェクト経由で行う
 */
export const env = {
  // 必須環境変数
  apiUrl: import.meta.env.VITE_API_URL,

  // オプション環境変数
  appUrl: import.meta.env.VITE_APP_URL,
  googleRecaptchaSiteKey: import.meta.env.VITE_GOOGLE_RECAPTCHA_SITE_KEY,
  onesignalAppId: import.meta.env.VITE_ONESIGNAL_APP_ID,
} as const;

/**
 * 環境変数チェックヘルパー
 */
export const hasGoogleRecaptcha = (): boolean => {
  return !!env.googleRecaptchaSiteKey;
};

export const hasOneSignal = (): boolean => {
  return !!env.onesignalAppId;
};
