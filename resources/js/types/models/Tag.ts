/**
 * Tag関連の型定義
 * Tag-related type definitions
 */

/**
 * タグ表示型
 * Tag display type
 */
export interface TagShow {
  id: number;
  name: string;
}

/**
 * マイページタグ編集型
 * Mypage tag edit type
 */
export interface TagMypageEdit {
  id: number;
  name: string;
  description: string | null;
  editable: boolean;
  created_by: {
    id: number;
    name: string;
  } | null;
  last_modified_by: {
    id: number;
    name: string;
  } | null;
  last_modified_at: string | null;
  created_at: string;
  updated_at: string;
  articles_count: number;
}

/**
 * 新規タグ型
 * New tag type
 */
export interface TagNew {
  id: null;
  name: null | string;
  description: null | string;
}
