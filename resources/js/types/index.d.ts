/**
 * グローバル型定義
 * Global type definitions
 * 
 * 後方互換性のため、既存のnamespace形式を維持しつつ、
 * 新しい構造化された型定義も利用可能にしています。
 * 
 * For backward compatibility, we maintain existing namespace format
 * while also making new structured type definitions available.
 * 
 * 新しいコードでは以下のようにインポートして使用してください:
 * For new code, import types like this:
 * import type { ArticleList, UserShow } from '@/types/models';
 * import type { ArticleListResponse } from '@/types/api';
 */

// Re-export organized types
export type * as Models from "./models";
export type * as Api from "./api";
export type * as Utils from "./utils";
export type * as Components from "./components";

// Import for type references
import type {
  CategoryType as _CategoryType,
  ArticlePostType as _ArticlePostType,
  ArticleStatus as _ArticleStatus,
  UserRole as _UserRole,
  AttachmentableType as _AttachmentableType,
  AttachmentType as _AttachmentType,
  SectionType as _SectionType,
  Count as _Count,
  SearchableOption as _SearchableOption,
  ArticleList as _ArticleList,
  ArticleShow as _ArticleShow,
  ArticleBase as _ArticleBase,
  ArticleRelational as _ArticleRelational,
  ArticleMypageShow as _ArticleMypageShow,
  ArticleMypageEdit as _ArticleMypageEdit,
  ArticleMypageBase as _ArticleMypageBase,
  ArticleMypageRelational as _ArticleMypageRelational,
  ArticleContentBase as _ArticleContentBase,
  ArticleContentAddonPost as _ArticleContentAddonPost,
  ArticleContentAddonIntroduction as _ArticleContentAddonIntroduction,
  ArticleContentMarkdown as _ArticleContentMarkdown,
  ArticleContentPage as _ArticleContentPage,
  ContentAddonPost as _ContentAddonPost,
  ContentAddonIntroduction as _ContentAddonIntroduction,
  ContentMarkdown as _ContentMarkdown,
  ContentPage as _ContentPage,
  SectionBase as _SectionBase,
  SectionText as _SectionText,
  SectionImage as _SectionImage,
  SectionUrl as _SectionUrl,
  SectionCaption as _SectionCaption,
  SearchableOption as _ArticleSearchableOption,
  UserShow as _UserShow,
  UserMypageEdit as _UserMypageEdit,
  UserMypageShow as _UserMypageShow,
  ProfileShow as _ProfileShow,
  ProfileEdit as _ProfileEdit,
  CategoryShow as _CategoryShow,
  CategoryMypageEdit as _CategoryMypageEdit,
  CategoryGrouping as _CategoryGrouping,
  TagShow as _TagShow,
  TagMypageEdit as _TagMypageEdit,
  TagNew as _TagNew,
  AttachmentShow as _AttachmentShow,
  AttachmentMypageEdit as _AttachmentMypageEdit,
  FileInfoShow as _FileInfoShow,
  FileInfoMypageEdit as _FileInfoMypageEdit,
} from "./models";

// Legacy global type aliases (for backward compatibility)
declare global {
  type CategoryType = _CategoryType;
  type ArticlePostType = _ArticlePostType;
  type ArticleStatus = _ArticleStatus;
  type UserRole = _UserRole;
  type AttachmentableType = _AttachmentableType;
  type AttachmentType = _AttachmentType;
  type SectionType = _SectionType;
  type Count = _Count;
  type SearchableOption = _SearchableOption;

  // Legacy namespace declarations (for backward compatibility)
  namespace Article {
    type List = _ArticleList;
    type Show = _ArticleShow;
    type Base = _ArticleBase;
    type Relational = _ArticleRelational;
    type MypageShow = _ArticleMypageShow;
    type MypageEdit = _ArticleMypageEdit;
    type MypageBase = _ArticleMypageBase;
    type MypageRelational = _ArticleMypageRelational;
  }

  namespace User {
    type Show = _UserShow;
    type MypageEdit = _UserMypageEdit;
    type MypageShow = _UserMypageShow;
  }

  namespace Profile {
    type Show = _ProfileShow;
    type Edit = _ProfileEdit;
  }

  namespace Category {
    type Show = _CategoryShow;
    type MypageEdit = _CategoryMypageEdit;
    type Grouping = _CategoryGrouping;
  }

  namespace Tag {
    type Show = _TagShow;
    type MypageEdit = _TagMypageEdit;
    type New = _TagNew;
  }

  namespace Attachment {
    type Show = _AttachmentShow;
    type MypageEdit = _AttachmentMypageEdit;
  }

  namespace FileInfo {
    type Show = _FileInfoShow;
    type MypageEdit = _FileInfoMypageEdit;
  }

  namespace ArticleContent {
    type Base = _ArticleContentBase;
    type AddonPost = _ArticleContentAddonPost;
    type AddonIntroduction = _ArticleContentAddonIntroduction;
    type Markdown = _ArticleContentMarkdown;
    type Page = _ArticleContentPage;

    namespace Section {
      type Base = _SectionBase;
      type Text = _SectionText;
      type Image = _SectionImage;
      type Url = _SectionUrl;
      type Caption = _SectionCaption;
    }

    type SearchableOption = _ArticleSearchableOption;
  }

  // Legacy Content type aliases (for backward compatibility)
  type ContentAddonPost = _ContentAddonPost;
  type ContentAddonIntroduction = _ContentAddonIntroduction;
  type ContentMarkdown = _ContentMarkdown;
  type ContentPage = _ContentPage;

  // Legacy Section type aliases (for backward compatibility)
  type SectionText = _SectionText;
  type SectionImage = _SectionImage;
  type SectionCaption = _SectionCaption;
  type SectionUrl = _SectionUrl;
}

