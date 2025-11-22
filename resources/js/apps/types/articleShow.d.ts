namespace ArticleShow {
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
    title: string;
    slug: string;
    post_type: Article.PostType;
    user: User;
    categories: Category.Search[];
    tags: Tag[];
    articles: RelationaArticle[];
    relatedArticles: RelationaArticle[];
    attachments: Attachment[];
    published_at: string | null;
    modified_at: string | null;
  };

  type Attachment = AttachmentShowable & {
    id: number;
    thumbnail: string;
    original_name: string;
    url: string;
    fileInfo: FileInfo | null;
  };

  type TagShowable = {
    id: number;
    name: string;
  };

  type RelationaArticle = {
    id: number;
    title: string;
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
      attachments: Attachment[];
    };
  };

  type FileInfo = {
    data: {
      dats: Record<string, string[]>;
      paks: Record<string, string[]>;
      readmes: Record<string, string[]>;
      tabs: Record<string, Record<string, string>>;
    };
  };
}
