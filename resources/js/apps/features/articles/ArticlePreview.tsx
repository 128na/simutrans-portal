import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { ArticleBase } from "../frontArticle/ArticleBase";

function toArticleShow(
  article: ArticleEdit.Article,
  user: ArticleEdit.User,
  categories: Category.Grouping,
  tags: TagEdit.Tag[],
  attachments: AttachmentEdit.Attachment[],
  relationalArticles: ArticleEdit.Relational[],
): ArticleShow.Article {
  const base = {
    id: article.id,
    title: article.title,
    slug: article.slug,
    post_type: article.post_type,
    user,
    categories: article.categories
      .map((id) =>
        Object.values(categories)
          .flat()
          .find((item) => item.id === id),
      )
      .filter((c): c is Category.Search => c !== undefined),
    tags: article.tags
      .map((id) => tags.find((t) => t.id === id))
      .filter((t) => t !== undefined),
    articles: article.articles
      .map((id) => relationalArticles.find((a) => a.id === id))
      .filter((a) => a !== undefined),
    relatedArticles: [],
    attachments,
    published_at: article.published_at,
    modified_at: article.modified_at,
  };

  switch (article.post_type) {
    case "addon-introduction":
      return {
        ...base,
        post_type: "addon-introduction",
        contents: article.contents,
      };

    case "addon-post":
      return {
        ...base,
        post_type: "addon-post",
        contents: article.contents,
      };

    case "page":
      return {
        ...base,
        post_type: "page",
        contents: article.contents,
      };

    case "markdown":
      return {
        ...base,
        post_type: "markdown",
        contents: article.contents,
      };
  }
}
export const ArticlePreview = () => {
  const article = useArticleEditor((s) => s.article);
  const user = useArticleEditor((s) => s.user);
  const attachments = useArticleEditor((s) => s.attachments);
  const categories = useArticleEditor((s) => s.categories);
  const tags = useArticleEditor((s) => s.tags);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  return (
    <div>
      <p className="p-4 mb-4 text-sm text-yellow-900 rounded-lg bg-yellow-50 border border-yellow-300 ">
        プレビュー表示ではリンクやボタンが反応しないようになっています。（マークダウン形式を除く）
      </p>

      <h2 className="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl mb-8 break-words">
        {article.title || "(タイトル)"}
      </h2>

      <ArticleBase
        article={toArticleShow(
          article,
          user,
          categories,
          tags,
          attachments,
          relationalArticles,
        )}
        preview={true}
      />
    </div>
  );
};
