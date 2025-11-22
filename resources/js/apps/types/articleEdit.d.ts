namespace ArticleEdit {
  type Article =
    | ({
        post_type: "addon-introduction";
        contents: ContentAddonIntroduction;
      } & Base)
    | ({ post_type: "addon-post"; contents: ContentAddonPost } & Base)
    | ({ post_type: "page"; contents: ContentPage } & Base)
    | ({ post_type: "markdown"; contents: ContentMarkdown } & Base);

  type Base = {
    id: number | null;
    user_id: number;
    title: string;
    slug: string;
    post_type: Article.PostType;
    status: Article.Status;
    categories: number[];
    tags: number[];
    articles: number[];
    attachments: number[];
    published_at: string | null;
    modified_at: string | null;
    created_at: string | null;
    updated_at: string | null;
  };

  type Relational = ArticleShow.RelationaArticle &
    SearchableOption & {
      id: number;
      user_id: number;
      user_name: string;
      title: string;
      slug: string;
    };

  type User = {
    id: number;
    name: string;
    nickname: string | null;
    role: User.Role;
    profile: {
      data: {
        avatar: number | null;
        website: string[];
        description: string | null;
      };
      attachments: ArticleShow.Attachment[];
    };
  };
}

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
