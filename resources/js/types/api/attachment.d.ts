/**
 * Attachment API関連の型定義
 * Attachment API-related type definitions
 */

import type {
  AttachmentShow,
  AttachmentMypageEdit,
} from "../models/Attachment";

/**
 * 添付ファイル一覧レスポンス
 * Attachment list response
 */
export interface AttachmentListResponse {
  attachments: AttachmentShow[];
}

/**
 * マイページ添付ファイル一覧レスポンス
 * Mypage attachment list response
 */
export interface AttachmentMypageListResponse {
  attachments: AttachmentMypageEdit[];
}

/**
 * 添付ファイルアップロードレスポンス
 * Attachment upload response
 */
export interface AttachmentUploadResponse {
  attachment: AttachmentMypageEdit;
  message?: string;
}

/**
 * 添付ファイル削除レスポンス
 * Attachment delete response
 */
export interface AttachmentDeleteResponse {
  message: string;
}
