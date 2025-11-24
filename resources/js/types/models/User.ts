/**
 * User関連の型定義
 * User-related type definitions
 */

/**
 * ユーザーロール
 * User role
 */
export type UserRole = "admin" | "user";

/**
 * ユーザー表示型（公開ページ）
 * User display type for public pages
 */
export interface UserShow {
  id: number;
  name: string;
  nickname: string | null;
  profile: import("./Profile").ProfileShow;
}

/**
 * マイページユーザー編集型
 * Mypage user edit type
 */
export interface UserMypageEdit {
  id: number;
  name: string;
  email: string;
  nickname: string | null;
  profile: import("./Profile").ProfileEdit;
}

/**
 * マイページユーザー表示型
 * Mypage user display type
 */
export interface UserMypageShow {
  id: number;
  name: string;
  nickname: string | null;
  role: UserRole;
  profile: import("./Profile").ProfileShow;
}
