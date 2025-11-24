/**
 * ロギングユーティリティ
 * 開発環境ではコンソールに出力し、本番環境では何もしない
 */

import { env } from "@/lib/env";

const isDevelopment = env.isDevelopment;

export const logger = {
  /**
   * デバッグログ（開発環境のみ）
   */
  debug: (message: string, ...args: unknown[]) => {
    if (isDevelopment) {
      console.log(`[DEBUG] ${message}`, ...args);
    }
  },

  /**
   * エラーログ
   * 開発環境: コンソール出力
   * 本番環境: 何もしない（将来的にエラートラッキングサービスへの送信を追加可能）
   */
  error: (message: string, error: unknown) => {
    if (isDevelopment) {
      console.error(`[ERROR] ${message}`, error);
    }
    // 本番環境でのエラートラッキング（Sentry等）は将来的に追加可能
    // if (!isDevelopment && window.Sentry) {
    //   window.Sentry.captureException(error);
    // }
  },

  /**
   * 警告ログ（開発環境のみ）
   */
  warn: (message: string, ...args: unknown[]) => {
    if (isDevelopment) {
      console.warn(`[WARN] ${message}`, ...args);
    }
  },
};
