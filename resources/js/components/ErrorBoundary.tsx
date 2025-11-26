import { Component, type ErrorInfo, type ReactNode } from "react";
import { handleError } from "@/lib/errorHandler";

interface Props {
  /** 子コンポーネント */
  children: ReactNode;
  /** エラー時に表示するフォールバックコンポーネント */
  fallback?: ReactNode;
  /** コンポーネント識別用の名前 */
  name?: string;
}

interface State {
  /** エラーが発生したかどうか */
  hasError: boolean;
}

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
export class ErrorBoundary extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = { hasError: false };
  }

  /**
   * エラーが発生した際に状態を更新する
   */
  static getDerivedStateFromError(): State {
    return { hasError: true };
  }

  /**
   * エラー情報をログに記録する
   */
  componentDidCatch(error: Error, errorInfo: ErrorInfo): void {
    handleError(error, {
      component: this.props.name || "ErrorBoundary",
      action: errorInfo.componentStack?.slice(0, 500) || "render",
      silent: true, // UIレンダリングエラーなのでalertは表示しない
    });
  }

  render(): ReactNode {
    if (this.state.hasError) {
      return (
        this.props.fallback || (
          <div className="p-4 text-center text-gray-600">
            <p>エラーが発生しました</p>
            <p className="text-sm mt-2">
              ページを再読み込みしてください。問題が続く場合はお問い合わせください。
            </p>
          </div>
        )
      );
    }

    return this.props.children;
  }
}
