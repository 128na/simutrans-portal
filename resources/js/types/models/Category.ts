/**
 * Category関連の型定義
 * Category-related type definitions
 */

/**
 * カテゴリタイプ
 * Category type
 */
export type CategoryType =
  | "pak"
  | "addon"
  | "pak128_position"
  | "license"
  | "page"
  | "double_slope";

/**
 * カテゴリ表示型
 * Category display type
 */
export interface CategoryShow {
  id: number;
  type: CategoryType;
  slug: string;
}

/**
 * マイページカテゴリ編集型
 * Mypage category edit type
 */
export interface CategoryMypageEdit {
  id: number;
  type: CategoryType;
  slug: string;
  need_admin: boolean;
}

/**
 * カテゴリグループ型
 * Category grouping type
 */
export type CategoryGrouping = {
  [K in CategoryType]: CategoryMypageEdit[];
};
