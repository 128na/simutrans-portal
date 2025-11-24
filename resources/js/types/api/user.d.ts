/**
 * User API関連の型定義
 * User API-related type definitions
 */

import type { UserShow, UserMypageShow, UserMypageEdit } from "../models/User";

/**
 * ユーザー詳細レスポンス
 * User detail response
 */
export interface UserShowResponse {
  user: UserShow;
}

/**
 * マイページユーザー情報レスポンス
 * Mypage user info response
 */
export interface UserMypageShowResponse {
  user: UserMypageShow;
}

/**
 * マイページユーザー編集レスポンス
 * Mypage user edit response
 */
export interface UserMypageEditResponse {
  user: UserMypageEdit;
}
