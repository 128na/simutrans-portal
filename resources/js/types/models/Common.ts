/**
 * 共通型定義
 * Common type definitions
 */

/**
 * 検索可能オプション型
 * Searchable option type
 */
export type SearchableOption<T = Record<string, unknown>> = {
  id: number;
} & T;
