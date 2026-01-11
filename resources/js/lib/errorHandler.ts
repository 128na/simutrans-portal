/**
 * 統一されたエラーハンドリングユーティリティ
 * フロントエンド全体で一貫性のあるエラー処理を提供する
 *
 * 【推奨利用方法】
 * - コンポーネント内: useToastの showError() を直接使用
 *   ```typescript
 *   const { showError } = useToast();
 *   try {
 *     await api.call();
 *   } catch (err) {
 *     showError(extractErrorMessage(err));
 *   }
 *   ```
 *
 * - ユーティリティ関数内: handleError() を使用
 *   ```typescript
 *   try {
 *     await someAsyncOperation();
 *   } catch (error) {
 *     handleError(error, { component: 'Service', action: 'fetch', silent: false });
 *   }
 *   ```
 */

import type { AxiosError } from "axios";
import { logger } from "@/utils/logger";

/**
 * エラーコンテキスト
 * エラーが発生した場所や操作を特定するための情報
 */
export interface ErrorContext {
  /** エラーが発生したコンポーネント名 */
  component?: string;
  /** エラーが発生した操作/アクション */
  action?: string;
  /** ユーザーへの通知をスキップするかどうか */
  silent?: boolean;
}

/**
 * アプリケーション固有のエラークラス
 * 標準のErrorを拡張してアプリケーション固有の情報を持つ
 */
export class AppError extends Error {
  constructor(
    message: string,
    public readonly code?: string,
    public readonly statusCode?: number
  ) {
    super(message);
    this.name = "AppError";
    // V8エンジン用のスタックトレース改善
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, AppError);
    }
  }
}

/**
 * バリデーションエラーのレスポンス型
 * Laravelのバリデーションエラーレスポンスに対応
 */
export interface ValidationErrorResponse {
  errors: Record<string, string[]>;
  message: string;
}

/**
 * エラーからユーザー向けメッセージを抽出する
 */
export const extractErrorMessage = (error: unknown): string => {
  if (error instanceof AppError) {
    return error.message;
  }

  if (isAxiosError(error)) {
    // APIレスポンスのエラーメッセージを優先的に取得
    const responseData = error.response?.data as
      | { error?: string; message?: string }
      | undefined;
    if (responseData?.error) {
      return responseData.error;
    }
    if (responseData?.message) {
      return responseData.message;
    }

    // ネットワークエラー
    if (!error.response) {
      return "ネットワークエラーが発生しました。接続を確認してください";
    }

    // HTTPステータスコードに基づくメッセージ
    const status = error.response?.status;
    switch (status) {
      case 401:
        return "認証が必要です。ログインしてください";
      case 403:
        return "この操作を行う権限がありません";
      case 404:
        return "リソースが見つかりませんでした";
      case 422:
        return "入力内容に問題があります";
      case 500:
        return "サーバーエラーが発生しました";
      default:
        return "エラーが発生しました";
    }
  }

  if (error instanceof Error) {
    return error.message;
  }

  return "予期しないエラーが発生しました";
};

/**
 * エラーをログに記録する（内部関数）
 *
 * 開発環境ではコンソールにログを出力し、
 * 本番環境では将来的に外部サービス（Sentry等）への送信を追加可能
 *
 * @param error - 発生したエラー
 * @param context - エラーコンテキスト（コンポーネント名、アクション等）
 */
const logError = (error: unknown, context?: ErrorContext): void => {
  const contextInfo = context
    ? ` [${context.component || "unknown"}${context.action ? `:${context.action}` : ""}]`
    : "";

  logger.error(`Error${contextInfo}`, error);
};

/**
 * Axiosエラーかどうかを型ガードで判定
 */
export const isAxiosError = (error: unknown): error is AxiosError => {
  return (
    typeof error === "object" &&
    error !== null &&
    "isAxiosError" in error &&
    (error as AxiosError).isAxiosError === true
  );
};

/**
 * バリデーションエラー（422）かどうかを判定
 * response.dataの存在も保証する
 */
export const isValidationError = (
  error: unknown
): error is AxiosError<ValidationErrorResponse> & {
  response: { data: ValidationErrorResponse };
} => {
  return (
    isAxiosError(error) &&
    error.response?.status === 422 &&
    error.response?.data !== undefined
  );
};

/**
 * 統一されたエラーハンドラー
 *
 * 注意: このハンドラーはユーティリティ関数として機能します。
 * コンポーネント内で使用する場合は、useToast() フックの showError() を
 * 直接使用する方が推奨されます（UIの一貫性とアクセシビリティが向上）。
 *
 * @param error - 発生したエラー
 * @param context - エラーコンテキスト（任意）
 *
 * @example
 * ```typescript
 * // コンポーネント内での推奨用法
 * const { showError } = useToast();
 * try {
 *   await someAsyncOperation();
 * } catch (error) {
 *   showError(extractErrorMessage(error));
 * }
 *
 * // ユーティリティ関数内
 * try {
 *   await someAsyncOperation();
 * } catch (error) {
 *   handleError(error, { component: 'Service', action: 'fetch' });
 * }
 * ```
 */
export const handleError = (error: unknown, context?: ErrorContext): void => {
  // ログ記録
  logError(error, context);

  // ユーザーへの通知（サイレントモードでない場合）
  if (!context?.silent) {
    const message = extractErrorMessage(error);
    // コンポーネント内の useToast() showError() が推奨される。
    // ここではユーティリティ関数からのエラーハンドリング対応
    // 本番環境での改善: Sentry等の外部サービスへの送信
    window.alert(message);
  }
};

/**
 * エラーハンドラー（通知なし版）
 * エラーをログに記録するが、ユーザーには通知しない
 *
 * @param error - 発生したエラー
 * @param context - エラーコンテキスト（任意）
 */
export const handleErrorSilent = (
  error: unknown,
  context?: ErrorContext
): void => {
  handleError(error, { ...context, silent: true });
};
