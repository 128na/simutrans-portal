/**
 * Attachment関連の型定義
 * Attachment-related type definitions
 */

/**
 * 添付可能な対象タイプ
 * Attachmentable target type
 */
export type AttachmentableType = "Article" | "Profile";

/**
 * 添付ファイルタイプ
 * Attachment file type
 */
export type AttachmentType = "image" | "video" | "text" | "file";

/**
 * 添付ファイル基本型
 * Base attachment type
 */
export interface Attachment {
  id: number;
  thumbnail: string;
  original_name: string;
  url: string;
}

/**
 * 添付ファイル表示型（公開ページ）
 * Attachment display type for public pages
 */
export interface AttachmentShow {
  id: number;
  thumbnail: string;
  original_name: string;
  url: string;
  fileInfo?: import("./FileInfo").FileInfoShow | null;
}

/**
 * マイページ添付ファイル編集型
 * Mypage attachment edit type
 */
export interface AttachmentMypageEdit {
  id: number;
  user_id: number;
  attachmentable_id: number;
  attachmentable_type: AttachmentableType;
  attachmentable: {
    id: number;
    title: string;
  } | null;
  type: AttachmentType;
  original_name: string;
  thumbnail: string;
  url: string;
  size: number;
  fileInfo?: import("./FileInfo").FileInfoMypageEdit;
  caption?: string;
  order?: number;
  created_at: string;
}
