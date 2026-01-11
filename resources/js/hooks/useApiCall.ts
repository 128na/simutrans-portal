import { useState } from "react";
import { useToast } from "@/hooks/useToast";
import { extractErrorMessage, isValidationError } from "@/lib/errorHandler";

/**
 * API呼び出し結果の型
 */
export interface ApiCallResult<T> {
  /** 成功時のデータ */
  data?: T;
  /** バリデーションエラー */
  validationErrors?: Record<string, string[]>;
  /** エラーが発生したかどうか */
  hasError: boolean;
}

/**
 * useApiCall Hookの戻り値
 */
export interface UseApiCallReturn {
  /** 現在API呼び出し中かどうか */
  isLoading: boolean;
  /** API呼び出しを実行する */
  call: <T>(
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
  ) => Promise<ApiCallResult<T>>;
}

/**
 * API呼び出しを統一的に処理するHook
 *
 * エラーハンドリング、ローディング状態管理、トースト通知を自動化
 *
 * @example
 * ```tsx
 * const { call, isLoading } = useApiCall();
 *
 * // 基本的な使い方
 * await call(
 *   () => axios.post('/api/v1/mylist', data),
 *   {
 *     onSuccess: () => refreshList(),
 *     successMessage: 'マイリストを作成しました'
 *   }
 * );
 *
 * // バリデーションエラーを処理
 * const result = await call(() => axios.post('/api/v1/mylist', data));
 * if (result.validationErrors) {
 *   // バリデーションエラーの処理
 * }
 * ```
 */
export const useApiCall = (): UseApiCallReturn => {
  const { showSuccess, showError } = useToast();
  const [isLoading, setIsLoading] = useState(false);

  const call = async <T>(
    apiCall: () => Promise<T>,
    options?: {
      onSuccess?: (data: T) => void;
      successMessage?: string;
      onError?: (error: unknown) => void;
      silent?: boolean;
    }
  ): Promise<ApiCallResult<T>> => {
    try {
      setIsLoading(true);
      const data = await apiCall();

      // 成功時の処理
      if (options?.successMessage && !options?.silent) {
        showSuccess(options.successMessage);
      }
      options?.onSuccess?.(data);

      return {
        data,
        hasError: false,
      };
    } catch (err) {
      // バリデーションエラー（422）の処理
      if (isValidationError(err)) {
        return {
          validationErrors: err.response.data.errors,
          hasError: true,
        };
      }

      // 一般的なエラーの処理
      if (!options?.silent) {
        showError(extractErrorMessage(err));
      }
      options?.onError?.(err);

      return {
        hasError: true,
      };
    } finally {
      setIsLoading(false);
    }
  };

  return { call, isLoading };
};
