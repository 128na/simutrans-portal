import { ReactNode } from "react";
import { ToastProvider } from "@/providers/ToastProvider";
import { ToastContainer } from "@/components/ui/ToastContainer";
import { ErrorBoundary } from "@/components/ErrorBoundary";

interface AppWrapperProps {
  children: ReactNode;
  boundaryName?: string;
}

/**
 * アプリケーション全体をラップするコンポーネント
 * ToastProvider、ToastContainer、ErrorBoundaryを統合
 *
 * @example
 * ```tsx
 * createRoot(app).render(
 *   <AppWrapper boundaryName="MyPage">
 *     <MyPageComponent />
 *   </AppWrapper>
 * );
 * ```
 */
export const AppWrapper = ({ children, boundaryName }: AppWrapperProps) => {
  return (
    <ToastProvider>
      <ErrorBoundary name={boundaryName}>
        {children}
        <ToastContainer />
      </ErrorBoundary>
    </ToastProvider>
  );
};
