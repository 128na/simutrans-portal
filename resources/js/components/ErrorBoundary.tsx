import { type ReactNode } from "react";
import { ErrorBoundary as ReactErrorBoundary } from "react-error-boundary";
import type { ErrorInfo } from "react";
import { handleError } from "@/lib/errorHandler";

interface Props {
  /** 子コンポーネント */
  children: ReactNode;
  /** エラー時に表示するフォールバックコンポーネント */
  fallback?: ReactNode;
  /** コンポーネント識別用の名前 */
  name?: string;
}

/**
 * デフォルトのフォールバックUI
 */
const DefaultFallback = () => (
  <div className="p-4 text-center text-secondary">
    <p>エラーが発生しました</p>
    <p className="text-sm mt-2">
      ページを再読み込みしてください。問題が続く場合はお問い合わせください。
    </p>
  </div>
);

/**
 * Reactエラーバウンダリーコンポーネント
 *
 * 子コンポーネントツリーでエラーが発生した場合に、
 * エラーをキャッチしてフォールバックUIを表示する
 *
 * @example
 * ```tsx
 * <ErrorBoundary name="MyPage" fallback={<ErrorFallback />}>
 *   <MyComponent />
 * </ErrorBoundary>
 * ```
 */
export const ErrorBoundary = ({ children, fallback, name }: Props) => {
  const handleOnError = (error: Error, info: ErrorInfo) => {
    handleError(error, {
      component: name || "ErrorBoundary",
      action: info.componentStack?.slice(0, 500) || "render",
      silent: true, // UIレンダリングエラーなのでalertは表示しない
    });
  };

  return (
    <ReactErrorBoundary
      fallback={fallback ?? <DefaultFallback />}
      onError={handleOnError}
    >
      {children}
    </ReactErrorBoundary>
  );
};
