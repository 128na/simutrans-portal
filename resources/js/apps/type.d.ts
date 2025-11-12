namespace Tag {
  type Search = SearchableOption & {
    id: number;
    name: string;
  };
  type Listing = Search & {
    id: number;
    name: string;
    description: string | null;
    editable: boolean;
    created_by: User.Minimum | null;
    last_modified_by: User.Minimum | null;
    last_modified_at: string | null;
    created_at: string;
    updated_at: string;
    articles_count: number;
  };
  type Creating = {
    id: null;
    name: null | string;
    description: null | string;
  };
}
type CategoryType =
  | "pak"
  | "addon"
  | "pak128_position"
  | "license"
  | "page"
  | "double_slope";

namespace Category {
  type Search = {
    id: number;
    type: CategoryType;
    slug: string;
    need_admin: boolean;
  };
  type Grouping = {
    [K in CategoryType]: Search[];
  };
}

namespace User {
  type Minimum = {
    id: number;
    name: string;
  };
  type Listing = Minimum & {
    nickname: string | null;
  };
  type WithRole = Listing & {
    role: "admin" | "user";
  };
  type ForEdit = WithRole & {
    email: string;
    profile: {
      id: number;
      data: {
        avatar: number | null;
        description: string | null;
        website: string[];
      };
    };
  };
}

namespace Article {
  type Analytics = SearchableOption & {
    id: number;
    title: string;
    published_at: string;
    modified_at: string;
  };
  type Relational = SearchableOption & {
    id: number;
    user_id: number;
    user_name: string;
    title: string;
    slug: string;
  };
  type Listing = {
    id: number;
    user_id: number;
    title: string;
    slug: string;
    post_type: PostType;
    status: Status;
    attachments: Attachment[];
    total_conversion_count: Count | null;
    total_view_count: Count | null;
    published_at: string | null;
    modified_at: string;
  };
  type Editing =
    | ({
        post_type: "addon-introduction";
        contents: ContentAddonIntroduction;
      } & BaseEditing)
    | ({ post_type: "addon-post"; contents: ContentAddonPost } & BaseEditing)
    | ({ post_type: "page"; contents: ContentPage } & BaseEditing)
    | ({ post_type: "markdown"; contents: ContentMarkdown } & BaseEditing);

  type BaseEditing = {
    id: number | null;
    user_id: number;
    title: string;
    slug: string;
    post_type: PostType;
    status: Status;
    categories: number[];
    tags: number[];
    articles: number[];
    attachments: number[];
    published_at: string | null;
    modified_at: string | null;
    created_at: string | null;
    updated_at: string | null;
  };
}

type Count = {
  id: number;
  article_id: number;
  user_id: number;
  type: 1 | 2 | 3 | 4;
  period: string;
  count: number;
};

type PostType = "addon-post" | "addon-introduction" | "page" | "markdown";
type Status = "publish" | "reservation" | "draft" | "trash" | "private";
type AttachmentableType = "Article" | "Profile";

type AttachmentType = "image" | "video" | "text" | "file";

type Attachment = SearchableOption & {
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
  file_info?: FileInfo;
  caption?: string;
  order?: number;
  created_at: string;
};

type FileInfo = {
  id: number;
  attachment_id: number;
  data: {
    readmes?: {
      [string]: string[];
    };
    paks?: {
      [string]: string[];
    };
    dats?: {
      [string]: string[];
    };
    tabs?: {
      [string]: { [string]: string };
    };
  };
  created_at: string;
  updated_at: string;
};

type Content = {
  thumbnail: number | null;
};
type ContentAddonPost = Content & {
  description: string | null;
  file: number | null;
  author: string | null;
  license: string | null;
  thanks: string | null;
};
type ContentAddonIntroduction = Content & {
  description: string | null;
  link: string | null;
  author: string | null;
  license: string | null;
  thanks: string | null;
  agreement: boolean;
  exclude_link_check: boolean;
};
type ContentMarkdown = Content & {
  markdown: string | null;
};
type ContentPage = Content & {
  sections: (SectionText | SectionImage | SectionCaption | SectionUrl)[];
};

type SectionType = "text" | "image" | "caption" | "url";
type Section = {
  type: SectionType | null;
};
type SectionText = Section & {
  type: "text";
  text: string | null;
};
type SectionImage = Section & {
  type: "image";
  id: number | null;
};
type SectionUrl = Section & {
  type: "url";
  url: string | null;
};
type SectionCaption = Section & {
  type: "caption";
  caption: string | null;
};

type SearchableOption = {
  id: number;
} & Record<string, string | null>;
