/**
 * マイリスト関連の型定義
 */

/**
 * マイリスト（基本情報）
 */
export interface MyListBase {
  id: number;
  user_id: number;
  title: string;
  note: string | null;
  is_public: boolean;
  slug: string | null;
  created_at: string;
  updated_at: string;
}

/**
 * マイリスト一覧表示用
 */
export interface MyListShow extends MyListBase {
  items_count?: number;
}

/**
 * マイリストアイテム（基本情報）
 */
export interface MyListItemBase {
  id: number;
  list_id: number;
  article_id: number;
  note: string | null;
  position: number;
  created_at: string;
  updated_at: string;
}

/**
 * マイリストアイテム一覧表示用
 */
export type MyListItemShow = Omit<
  MyListItemBase,
  "list_id" | "article_id" | "updated_at"
> & {
  article: {
    id: number;
    slug: string;
    title: string;
    url?: string;
    user: {
      id: number;
      name: string;
      slug: string;
      profile: {
        avatar: string | null;
      } | null;
    };
    thumbnail:
      | {
          url: string;
          alt: string | null;
        }
      | string
      | null;
    file: {
      url: string;
      size: number;
    } | null;
    published_at?: string;
  };
};

/**
 * マイリスト作成リクエスト
 */
export interface MyListCreateRequest {
  title: string;
  note?: string | null;
  is_public?: boolean;
}

/**
 * マイリスト更新リクエスト
 */
export interface MyListUpdateRequest {
  title: string;
  note?: string | null;
  is_public?: boolean;
}

/**
 * マイリストアイテム追加リクエスト
 */
export interface MyListItemCreateRequest {
  article_id: number;
  note?: string | null;
}

/**
 * マイリストアイテム更新リクエスト
 */
export interface MyListItemUpdateRequest {
  note?: string | null;
  position?: number;
}

/**
 * マイリストアイテム並び替えリクエスト
 */
export interface MyListItemReorderRequest {
  items: Array<{
    id: number;
    position: number;
  }>;
}
