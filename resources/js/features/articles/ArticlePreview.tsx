import { useArticleEditor } from "@/hooks/useArticleEditor";
import { ArticleBase } from "./components/ArticleBase";
import TextBadge from "@/components/ui/TextBadge";
import TextSub from "@/components/ui/TextSub";

function toArticleShow(
  article: Article.MypageEdit,
  user: User.MypageShow,
  categories: Category.Grouping,
  tags: Tag.MypageEdit[],
  attachments: Attachment.MypageEdit[],
  relationalArticles: Article.MypageRelational[]
): Article.Show {
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
          .find((item) => item.id === id)
      )
      .filter((c): c is Category.MypageEdit => c !== undefined),
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
      <div className="mt-2 mb-8">
        <TextBadge className="bg-yellow-500">プレビュー表示</TextBadge>
        <TextSub>
          プレビュー表示ではリンクやボタンが反応しないようになっています。（マークダウン形式を除く）
        </TextSub>
      </div>

      <h2 className="title-xl mb-8 break-words">
        {article.title || "(タイトル)"}
      </h2>

      <ArticleBase
        article={toArticleShow(
          article,
          user,
          categories,
          tags,
          attachments,
          relationalArticles
        )}
        preview={true}
      />
    </div>
  );
};
