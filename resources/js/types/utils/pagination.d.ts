/**
 * ページネーション関連型
 * Pagination-related types
 */

/**
 * ページネーション情報
 * Pagination metadata
 */
export interface PaginationInfo {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

/**
 * ページネーションリンク情報
 * Pagination link information
 */
export interface PaginationLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

/**
 * ページネーションパラメータ
 * Pagination query parameters
 */
export interface PaginationParams {
  page?: number;
  per_page?: number;
}
