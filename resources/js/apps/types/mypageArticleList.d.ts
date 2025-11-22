namespace MypageArticleList {
  type Article = {
    id: number;
    user_id: number;
    title: string;
    slug: string;
    post_type: Article.PostType;
    status: Article.Status;
    attachments: AttachmentEdit.Attachment[];
    total_conversion_count: Count | null;
    total_view_count: Count | null;
    published_at: string | null;
    modified_at: string;
  };

  type User = {
    id: number;
    name: string;
    nickname: string | null;
  };

  type Count = {
    id: number;
    article_id: number;
    user_id: number;
    type: 1 | 2 | 3 | 4;
    period: string;
    count: number;
  };
}
