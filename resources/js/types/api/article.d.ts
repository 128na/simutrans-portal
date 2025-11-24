/**
 * Article API関連の型定義
 * Article API-related type definitions
 */

import type {
  ArticleList,
  ArticleShow,
  ArticleMypageShow,
  ArticleMypageEdit,
  ArticleMypageRelational,
  ArticleStatus,
} from "../models/Article";
import type { PaginatedResponse } from "../utils/response";

/**
 * 記事一覧取得リクエストパラメータ
 * Article list request parameters
 */
export interface ArticleListParams {
  page?: number;
  per_page?: number;
  status?: ArticleStatus;
  q?: string;
  tag?: string;
  category?: string;
  user?: string;
}

/**
 * 記事一覧レスポンス
 * Article list response
 */
export type ArticleListResponse = PaginatedResponse<ArticleList>;

/**
 * 記事詳細レスポンス
 * Article detail response
 */
export interface ArticleShowResponse {
  article: ArticleShow;
}

/**
 * マイページ記事一覧レスポンス
 * Mypage article list response
 */
export interface ArticleMypageListResponse {
  articles: ArticleMypageShow[];
}

/**
 * マイページ記事詳細レスポンス
 * Mypage article detail response
 */
export interface ArticleMypageShowResponse {
  article: ArticleMypageEdit;
  relational_articles: ArticleMypageRelational[];
}

/**
 * 記事保存リクエスト
 * Article save request
 */
export interface ArticleSaveRequest {
  article: Partial<ArticleMypageEdit>;
  should_notify?: boolean;
  without_update_modified_at?: boolean;
  follow_redirect?: boolean;
}

/**
 * 記事保存レスポンス
 * Article save response
 */
export interface ArticleSaveResponse {
  article_id: number;
  message?: string;
}

/**
 * 記事削除レスポンス
 * Article delete response
 */
export interface ArticleDeleteResponse {
  message: string;
}
