/**
 * Tag API関連の型定義
 * Tag API-related type definitions
 */

import type { TagShow, TagMypageEdit } from "../models/Tag";

/**
 * タグ一覧レスポンス
 * Tag list response
 */
export interface TagListResponse {
  tags: TagShow[];
}

/**
 * マイページタグ一覧レスポンス
 * Mypage tag list response
 */
export interface TagMypageListResponse {
  tags: TagMypageEdit[];
}

/**
 * タグ作成リクエスト
 * Tag create request
 */
export interface TagCreateRequest {
  name: string;
  description?: string | null;
}

/**
 * タグ更新リクエスト
 * Tag update request
 */
export interface TagUpdateRequest {
  description: string | null;
}

/**
 * タグ保存レスポンス
 * Tag save response
 */
export interface TagSaveResponse {
  tag: TagMypageEdit;
  message?: string;
}

/**
 * タグ削除レスポンス
 * Tag delete response
 */
export interface TagDeleteResponse {
  message: string;
}
