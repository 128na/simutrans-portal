import { useState } from "react";
import { useToast } from "@/hooks/useToast";
import {
  extractErrorMessage,
  isValidationError,
  type ValidationErrorResponse,
} from "@/lib/errorHandler";
import type { AxiosError } from "axios";

/**
 * モデル編集・作成フォーム用の状態管理 Hook
 *
 * バリデーションエラー管理、ローディング状態、フォーム送信の標準パターンを提供
 *
 * @example
 * ```tsx
 * const { error, isLoading, setError, getError, handleSave } = useModelModal();
 *
 * const onSave = async () => {
 *   await handleSave(
 *     () => axios.post('/api/tags', { name, description }),
 *     {
 *       successMessage: 'タグを保存しました',
 *       onSuccess: () => refreshTags(),
 *     }
 *   );
 * };
 *
 * return (
 *   <>
 *     {error && <TextError>{error}</TextError>}
 *     {getError('name') && <TextError>{getError('name')?.join('\n')}</TextError>}
 *     <Button onClick={onSave} disabled={isLoading}>
 *       {isLoading ? '保存中...' : '保存'}
 *     </Button>
 *   </>
 * );
 * ```
 */
export const useModelModal = () => {
  const { showSuccess, showError } = useToast();
  const [isLoading, setIsLoading] = useState(false);
  const [generalError, setGeneralError] = useState<string | null>(null);
  const [validationErrors, setValidationErrors] = useState<Record<
    string,
    string[]
  > | null>(null);

  /**
   * エラーメッセージを取得
   * @param fieldName - フィールド名（バリデーションエラーがある場合）
   */
  const getError = (fieldName?: string): string[] | string | null => {
    if (fieldName && validationErrors?.[fieldName]) {
      return validationErrors[fieldName];
    }
    if (!fieldName && generalError) {
      return generalError;
    }
    return null;
  };

  /**
   * 汎用エラーを設定
   */
  const setError = (error: string | null) => {
    setGeneralError(error);
  };

  /**
   * バリデーションエラーを設定
   */
  const setValidationErrors_ = (errors: Record<string, string[]> | null) => {
    setValidationErrors(errors);
  };

  /**
   * API呼び出しを実行し、エラーハンドリングを行う
   */
  const handleSave = async <T>(
    apiCall: () => Promise<T>,
    options?: {
      /** 成功時のコールバック */
      onSuccess?: (data: T) => void;
      /** 成功時のトーストメッセージ */
      successMessage?: string;
      /** エラー時のコールバック */
      onError?: (error: unknown) => void;
      /** トースト通知を表示しない */
      silent?: boolean;
    }
  ): Promise<void> => {
    try {
      setIsLoading(true);
      setGeneralError(null);
      setValidationErrors(null);

      const data = await apiCall();

      if (options?.successMessage && !options?.silent) {
        showSuccess(options.successMessage);
      }
      options?.onSuccess?.(data);
    } catch (err) {
      // バリデーションエラー（422）の処理
      if (isValidationError(err)) {
        setValidationErrors(err.response.data.errors);
      } else {
        // 一般的なエラーの処理
        const message = extractErrorMessage(err);
        setGeneralError(message);
        if (!options?.silent) {
          showError(message);
        }
        options?.onError?.(err);
      }
    } finally {
      setIsLoading(false);
    }
  };

  return {
    /** ローディング状態 */
    isLoading,
    /** 汎用エラーメッセージ（バリデーションエラーがない場合） */
    error: generalError,
    /** バリデーションエラー一覧 */
    errors: validationErrors,
    /** エラーメッセージを取得（フィールド名指定時はバリデーションエラーを取得） */
    getError,
    /** 汎用エラーを設定 */
    setError,
    /** バリデーションエラーを設定 */
    setValidationErrors: setValidationErrors_,
    /** API呼び出しとエラーハンドリングを実行 */
    handleSave,
  };
};
