type Tag = {
  id: number;
  name: string;
  description: string | null;
  editable: boolean;
  created_by: SimpleUser | null;
  last_modified_by: SimpleUser | null;
  last_modified_at: string | null;
  created_at: string;
  updated_at: string;
  articles_count: number;
};
type NewTag = {
  id: null;
  name: null | string;
  description: null | string;
};

type SimpleUser = {
  id: number;
  name: string;
};

type Count = {
  id: number;
  article_id: number;
  user_id: number;
  type: 1 | 2 | 3 | 4;
  period: string;
  count: number;
};

type ListingArticle = {
  id: number;
  user_id: number;
  title: string;
  slug: string;
  post_type: "addon-post" | "addon-introduction" | "page" | "markdown";
  status: "publish" | "reservation" | "draft" | "trash" | "private";
  attachments: Attachment[];
  total_conversion_count: Count | null;
  total_view_count: Count | null;
  published_at: string | null;
  modified_at: string;
};

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

type User = SimpleUser & {
  nickname: string | null;
};

type EditArticle = {
  id: number;
  user_id: number;
  title: string;
  slug: string;
  post_type: "addon-post" | "addon-introduction" | "page" | "markdown";
  status: "publish" | "reservation" | "draft" | "trash" | "private";
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

type Content = {
  thumbnail: number;
};
type ContentAddonPost = Content & {
  description: string;
  file: number;
  author: string | null;
  license: string | null;
  thanks: string | null;
};
type ContentAddonIntroduction = Content & {
  description: string;
  link: string;
  author: string;
  license: string | null;
  thanks: string | null;
  agreement: boolean;
  exclude_link_check: boolean;
};
type ContentMarkdown = Content & {
  markdown: string;
};
type ContentPage = Content & {
  sections: (SectionText | SectionImage | SectionCaption | SectionUrl)[];
};

type Section = {
  type: string;
};
type SectionText = Section & {
  type: "text";
  text: string;
};
type SectionImage = Section & {
  type: "image";
  image: number;
};
type SectionUrl = Section & {
  type: "url";
  url: string;
};

type SectionCaption = Section & {
  type: "caption";
  caption: string;
};
