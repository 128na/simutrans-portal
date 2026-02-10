/**
 * Article関連の型定義
 * Article-related type definitions
 */

/**
 * 記事の投稿タイプ
 * Article post type
 */
export type ArticlePostType =
  | "addon-post"
  | "addon-introduction"
  | "page"
  | "markdown";

/**
 * 記事のステータス
 * Article status
 */
export type ArticleStatus =
  | "publish"
  | "reservation"
  | "draft"
  | "trash"
  | "private";

/**
 * セクションタイプ
 * Section type for page post type
 */
export type SectionType = "text" | "image" | "caption" | "url";

/**
 * 記事一覧用の型（公開ページ）
 * Article list type for public pages
 */
export interface ArticleList {
  id: number | null;
  title: string;
  url: string;
  thumbnail: string;
  description: string;
  download_url?: string;
  addon_page_url?: string;
  user: import("./User").UserShow;
  categories: import("./Category").CategoryShow[];
  tags: import("./Tag").TagShow[];
  published_at: string | null;
  modified_at: string | null;
}

/**
 * 記事詳細の基本型
 * Base article detail type
 */
export interface ArticleBase {
  id: number | null;
  title: string;
  slug: string;
  post_type: ArticlePostType;
  download_url?: string;
  addon_page_url?: string;
  user: import("./User").UserShow;
  categories: import("./Category").CategoryShow[];
  tags: import("./Tag").TagShow[];
  articles: ArticleRelational[];
  relatedArticles: ArticleRelational[];
  attachments: import("./Attachment").AttachmentShow[];
  published_at: string | null;
  modified_at: string | null;
}

/**
 * 関連記事型
 * Related article type
 */
export interface ArticleRelational {
  id: number;
  title: string;
}

/**
 * 記事詳細型（公開ページ）
 * Article detail type for public pages
 */
export type ArticleShow =
  | ({
      post_type: "addon-introduction";
      contents: ContentAddonIntroduction;
    } & ArticleBase)
  | ({ post_type: "addon-post"; contents: ContentAddonPost } & ArticleBase)
  | ({ post_type: "page"; contents: ContentPage } & ArticleBase)
  | ({ post_type: "markdown"; contents: ContentMarkdown } & ArticleBase);

/**
 * マイページ記事一覧型
 * Mypage article list type
 */
export interface ArticleMypageShow {
  id: number;
  user_id: number;
  title: string;
  slug: string;
  post_type: ArticlePostType;
  status: ArticleStatus;
  attachments: import("./Attachment").AttachmentMypageEdit[];
  total_conversion_count: import("./Count").Count | null;
  total_view_count: import("./Count").Count | null;
  published_at: string | null;
  modified_at: string;
}

/**
 * マイページ記事編集基本型
 * Mypage article edit base type
 */
export interface ArticleMypageBase {
  id: number | null;
  user_id: number;
  title: string;
  slug: string;
  post_type: ArticlePostType;
  status: ArticleStatus;
  categories: number[];
  tags: number[];
  articles: number[];
  attachments: number[];
  published_at: string | null;
  modified_at: string | null;
  created_at: string | null;
  updated_at: string | null;
}

/**
 * マイページ記事編集型
 * Mypage article edit type
 */
export type ArticleMypageEdit =
  | ({
      post_type: "addon-introduction";
      contents: ArticleContentAddonIntroduction;
    } & ArticleMypageBase)
  | ({
      post_type: "addon-post";
      contents: ArticleContentAddonPost;
    } & ArticleMypageBase)
  | ({
      post_type: "page";
      contents: ArticleContentPage;
    } & ArticleMypageBase)
  | ({
      post_type: "markdown";
      contents: ArticleContentMarkdown;
    } & ArticleMypageBase);

/**
 * マイページ記事関連型
 * Mypage article relational type
 */
export interface ArticleMypageRelational {
  id: number;
  user_id: number;
  user_name: string;
  title: string;
  slug: string;
}

// ===== Article Content Types =====

/**
 * 記事コンテンツ基本型
 * Article content base type
 */
export interface ArticleContentBase {
  thumbnail: number | null;
}

/**
 * アドオン投稿コンテンツ型
 * Addon post content type
 */
export interface ArticleContentAddonPost extends ArticleContentBase {
  description: string | null;
  file: number | null;
  author: string | null;
  license: string | null;
  thanks: string | null;
}

/**
 * アドオン紹介コンテンツ型
 * Addon introduction content type
 */
export interface ArticleContentAddonIntroduction extends ArticleContentBase {
  description: string | null;
  link: string | null;
  author: string | null;
  license: string | null;
  thanks: string | null;
  agreement: boolean;
  exclude_link_check: boolean;
}

/**
 * Markdownコンテンツ型
 * Markdown content type
 */
export interface ArticleContentMarkdown extends ArticleContentBase {
  markdown: string | null;
}

/**
 * ページコンテンツ型
 * Page content type
 */
export interface ArticleContentPage extends ArticleContentBase {
  sections: (SectionText | SectionImage | SectionCaption | SectionUrl)[];
}

/**
 * 公開用コンテンツ型のエイリアス
 * Public content type aliases (for backward compatibility)
 */
export type ContentAddonPost = ArticleContentAddonPost;
export type ContentAddonIntroduction = ArticleContentAddonIntroduction;
export type ContentMarkdown = ArticleContentMarkdown;
export type ContentPage = ArticleContentPage;

// ===== Section Types =====

/**
 * セクション基本型
 * Section base type
 */
export interface SectionBase {
  type: SectionType | null;
}

/**
 * テキストセクション型
 * Text section type
 */
export interface SectionText extends SectionBase {
  type: "text";
  text: string | null;
}

/**
 * 画像セクション型
 * Image section type
 */
export interface SectionImage extends SectionBase {
  type: "image";
  id: number | null;
}

/**
 * URLセクション型
 * URL section type
 */
export interface SectionUrl extends SectionBase {
  type: "url";
  url: string | null;
}

/**
 * キャプションセクション型
 * Caption section type
 */
export interface SectionCaption extends SectionBase {
  type: "caption";
  caption: string | null;
}

/**
 * 検索可能オプション型
 * Searchable option type
 */
export type SearchableOption = {
  id: number;
  [key: string]: string | number | boolean | object | null | undefined;
};
