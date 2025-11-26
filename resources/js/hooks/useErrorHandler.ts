import { useCallback } from "react";
import {
  handleError,
  handleErrorSilent,
  extractErrorMessage,
  isValidationError,
  type ErrorContext,
} from "@/lib/errorHandler";

/**
 * エラーハンドリング用のカスタムフック
 *
 * コンポーネント内でエラーを統一的に処理するためのフック
 *
 * @param defaultContext - デフォルトのエラーコンテキスト
 *
 * @example
 * ```tsx
 * const { handleErrorWithContext, handleSilent, getMessage } = useErrorHandler({
 *   component: 'ArticleEdit',
 * });
 *
 * const save = async () => {
 *   try {
 *     await axios.post('/api/articles', data);
 *   } catch (error) {
 *     handleErrorWithContext(error, { action: 'save' });
 *   }
 * };
 * ```
 */
export const useErrorHandler = (defaultContext?: ErrorContext) => {
  /**
   * エラーを処理する（ユーザー通知あり）
   */
  const handleErrorWithContext = useCallback(
    (error: unknown, additionalContext?: ErrorContext) => {
      handleError(error, {
        ...defaultContext,
        ...additionalContext,
      });
    },
    [defaultContext]
  );

  /**
   * エラーを処理する（サイレント、ユーザー通知なし）
   */
  const handleSilent = useCallback(
    (error: unknown, additionalContext?: ErrorContext) => {
      handleErrorSilent(error, {
        ...defaultContext,
        ...additionalContext,
      });
    },
    [defaultContext]
  );

  /**
   * エラーからメッセージを取得する
   */
  const getMessage = useCallback((error: unknown): string => {
    return extractErrorMessage(error);
  }, []);

  /**
   * バリデーションエラーかどうかを判定
   */
  const isValidation = useCallback((error: unknown): boolean => {
    return isValidationError(error);
  }, []);

  return {
    handleErrorWithContext,
    handleSilent,
    getMessage,
    isValidation,
  };
};
