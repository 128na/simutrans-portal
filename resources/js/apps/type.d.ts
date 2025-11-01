namespace Tag {
  type Listing = {
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

namespace User {
  type Minimum = {
    id: number;
    name: string;
  };
  type Listing = {
    id: number;
    name: string;
    nickname: string | null;
  };
  type WithRole = {
    id: number;
    name: string;
    nickname: string | null;
    role: "admin" | "user";
  };
}

namespace Article {
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
  type Editing = {
    id: number;
    user_id: number;
    title: string;
    slug: string;
    post_type: PostType;
    status: Status;
    contents:
      | ContentAddonPost
      | ContentAddonIntroduction
      | ContentPage
      | ContentMarkdown;
    published_at: string | null;
    modified_at: string;
    created_at: string;
    updated_at: string;
  };
  type Creating = {
    id: null;
    user_id: number;
    title: string | null;
    slug: string | null;
    post_type: PostType | null;
    status: Status;
    contents:
      | null
      | ContentAddonPost
      | ContentAddonIntroduction
      | ContentPage
      | ContentMarkdown;
    published_at: string | null;
    modified_at: null;
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

type Attachment = {
  id: number;
  user_id: number;
  attachmentable_id: number;
  attachmentable_type: "App\\Models\\Article" | "App\\Models\\Profile";
  caption: string | null;
  original_name: string;
  order: number;
  created_at: string;
  updated_at: string;
};

type Content = {
  thumbnail: number;
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

type Section = {
  type: string | null;
};
type SectionText = Section & {
  type: "text";
  text: string | null;
};
type SectionImage = Section & {
  type: "image";
  image: number | null;
};
type SectionUrl = Section & {
  type: "url";
  url: string | null;
};

type SectionCaption = Section & {
  type: "caption";
  caption: string | null;
};
