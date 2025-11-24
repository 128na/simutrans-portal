/**
 * Profile関連の型定義
 * Profile-related type definitions
 */

/**
 * プロフィール表示型
 * Profile display type
 */
export interface ProfileShow {
  id: number;
  data: {
    avatar: number | null;
    description: string | null;
    website: string[];
  };
  attachments: import("./Attachment").Attachment[];
}

/**
 * プロフィール編集型
 * Profile edit type
 */
export interface ProfileEdit {
  id: number;
  data: {
    avatar: number | null;
    description: string | null;
    website: string[];
  };
}
