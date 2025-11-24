/**
 * Category API関連の型定義
 * Category API-related type definitions
 */

import type {
  CategoryShow,
  CategoryMypageEdit,
  CategoryGrouping,
} from "../models/Category";

/**
 * カテゴリ一覧レスポンス
 * Category list response
 */
export interface CategoryListResponse {
  categories: CategoryShow[];
}

/**
 * マイページカテゴリ一覧レスポンス
 * Mypage category list response
 */
export interface CategoryMypageListResponse {
  categories: CategoryMypageEdit[];
}

/**
 * マイページカテゴリグループレスポンス
 * Mypage category grouping response
 */
export interface CategoryMypageGroupingResponse {
  categories: CategoryGrouping;
}
