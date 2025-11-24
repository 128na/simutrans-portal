/**
 * Count関連の型定義
 * Count-related type definitions
 */

/**
 * カウント型（閲覧数・コンバージョン数）
 * Count type for views and conversions
 */
export interface Count {
  id: number;
  article_id: number;
  user_id: number;
  type: 1 | 2 | 3 | 4;
  period: string;
  count: number;
}
