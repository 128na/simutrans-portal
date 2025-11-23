type CategoryType =
  | "pak"
  | "addon"
  | "pak128_position"
  | "license"
  | "page"
  | "double_slope";

type ArticlePostType =
  | "addon-post"
  | "addon-introduction"
  | "page"
  | "markdown";

type ArticleStatus = "publish" | "reservation" | "draft" | "trash" | "private";

type UserRole = "admin" | "user";
type AttachmentableType = "Article" | "Profile";
type AttachmentType = "image" | "video" | "text" | "file";
type SectionType = "text" | "image" | "caption" | "url";

type Count = {
  id: number;
  article_id: number;
  user_id: number;
  type: 1 | 2 | 3 | 4;
  period: string;
  count: number;
};
type SearchableOption = {
  id: number;
} & Record<string, string | number | boolean | object | null>;

namespace Article {
  type List = {
    id: number | null;
    title: string;
    url: string;
    thumbnail: string;
    description: string;
    user: User.Show;
    categories: Category.Show[];
    tags: Tag.Show[];
    published_at: string | null;
    modified_at: string | null;
  };

  type Show =
    | ({
        post_type: "addon-introduction";
        contents: ContentAddonIntroduction;
      } & Base)
    | ({ post_type: "addon-post"; contents: ContentAddonPost } & Base)
    | ({ post_type: "page"; contents: ContentPage } & Base)
    | ({ post_type: "markdown"; contents: ContentMarkdown } & Base);

  type Base = {
    id: number | null;
    title: string;
    slug: string;
    post_type: ArticlePostType;
    user: User.Show;
    categories: Category.Show[];
    tags: Tag.Show[];
    articles: Relational[];
    relatedArticles: Relational[];
    attachments: Attachment.Show[];
    published_at: string | null;
    modified_at: string | null;
  };

  type Relational = {
    id: number;
    title: string;
  };

  type MypageShow = {
    id: number;
    user_id: number;
    title: string;
    slug: string;
    post_type: ArticlePostType;
    status: ArticleStatus;
    attachments: Attachment.MypageEdit[];
    total_conversion_count: Count | null;
    total_view_count: Count | null;
    published_at: string | null;
    modified_at: string;
  };

  type MypageEdit =
    | ({
        post_type: "addon-introduction";
        contents: ArticleContent.AddonIntroduction;
      } & MypageBase)
    | ({
        post_type: "addon-post";
        contents: ArticleContent.AddonPost;
      } & MypageBase)
    | ({ post_type: "page"; contents: ArticleContent.Page } & MypageBase)
    | ({
        post_type: "markdown";
        contents: ArticleContent.Markdown;
      } & MypageBase);

  type MypageBase = {
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
  };

  type MypageRelational = {
    id: number;
    user_id: number;
    user_name: string;
    title: string;
    slug: string;
  };
}

namespace User {
  type Show = {
    id: number;
    name: string;
    nickname: string | null;
    profile: Profile.Show;
  };

  type MypageEdit = {
    id: number;
    name: string;
    email: string;
    nickname: string | null;
    profile: Profile.Edit;
  };

  type MypageShow = {
    id: number;
    name: string;
    nickname: string | null;
    role: UserRole;
    profile: Profile.Show;
  };
}

namespace Profile {
  type Show = {
    id: number;
    data: {
      avatar: number | null;
      description: string | null;
      website: string[];
    };
    attachments: Attachment[];
  };
  type Edit = {
    id: number;
    data: {
      avatar: number | null;
      description: string | null;
      website: string[];
    };
  };
}

namespace Category {
  type Show = {
    id: number;
    type: CategoryType;
    slug: string;
  };
  type MypageEdit = {
    id: number;
    type: CategoryType;
    slug: string;
    need_admin: boolean;
  };
  type Grouping = {
    [K in CategoryType]: MypageEdit[];
  };
}

namespace Tag {
  type Show = {
    id: number;
    name: string;
  };
  type MypageEdit = {
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
  };
  type New = {
    id: null;
    name: null | string;
    description: null | string;
  };
}

namespace Attachment {
  type Show = {
    id: number;
    thumbnail: string;
    original_name: string;
    url: string;
    fileInfo?: FileInfo.Show | null;
  };

  type MypageEdit = {
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
    fileInfo?: FileInfo.MypageEdit;
    caption?: string;
    order?: number;
    created_at: string;
  };
}

namespace FileInfo {
  type Show = {
    data: {
      dats: Record<string, string[]>;
      tabs: Record<string, Record<string, string>>;
    };
  };
  type MypageEdit = {
    data: {
      dats: Record<string, string[]>;
      tabs: Record<string, Record<string, string>>;
      paks: Record<string, string[]>;
      readmes: Record<string, string[]>;
    };
  };
}

namespace ArticleContent {
  type Base = {
    thumbnail: number | null;
  };
  type AddonPost = Base & {
    description: string | null;
    file: number | null;
    author: string | null;
    license: string | null;
    thanks: string | null;
  };
  type AddonIntroduction = Base & {
    description: string | null;
    link: string | null;
    author: string | null;
    license: string | null;
    thanks: string | null;
    agreement: boolean;
    exclude_link_check: boolean;
  };
  type Markdown = Base & {
    markdown: string | null;
  };
  type Page = Base & {
    sections: (SectionText | SectionImage | SectionCaption | SectionUrl)[];
  };

  namespace Section {
    type Base = {
      type: SectionType | null;
    };
    type Text = Base & {
      type: "text";
      text: string | null;
    };
    type Image = Base & {
      type: "image";
      id: number | null;
    };
    type Url = Base & {
      type: "url";
      url: string | null;
    };
    type Caption = Base & {
      type: "caption";
      caption: string | null;
    };
  }

  type SearchableOption = {
    id: number;
  } & Record<string, string | null>;
}
