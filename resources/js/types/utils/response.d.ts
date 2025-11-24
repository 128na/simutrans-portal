/**
 * 共通APIレスポンス型
 * Common API response types
 */

/**
 * 基本的なAPIレスポンス型
 * Basic API response wrapper
 */
export interface ApiResponse<T> {
  data: T;
  message?: string;
}

/**
 * ページネーション付きレスポンス型
 * Paginated response type (Laravel default structure)
 */
export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  links?: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
}

/**
 * バリデーションエラーレスポンス型
 * Validation error response (Laravel 422 error format)
 */
export interface ValidationError {
  message: string;
  errors: Record<string, string[]>;
}

/**
 * 標準的なエラーレスポンス型
 * Standard error response
 */
export interface ErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
  exception?: string;
  file?: string;
  line?: number;
  trace?: unknown[];
}
