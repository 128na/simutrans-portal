import { SelectableSearch } from "@/components/form/SelectableSearch";
import { FormCaption } from "@/components/ui/FormCaption";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { useAxiosError } from "@/hooks/useAxiosError";

/** サーバー側の `article.articles` の max と合わせる */
export const RELATED_ARTICLES_LIMIT = 10;

export const RELATED_ARTICLES_ERROR_KEY = "article.articles";

export const RelatedArticles = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);
  const { getErrorTree } = useAxiosError();

  return (
    <div>
      <FormCaption>関連記事</FormCaption>
      <TextError>{getErrorTree(RELATED_ARTICLES_ERROR_KEY)}</TextError>
      <SelectableSearch
        labelKey="title"
        options={relationalArticles}
        selectedIds={article.articles}
        maxSelected={RELATED_ARTICLES_LIMIT}
        onChange={(articleIds) =>
          update((draft) => (draft.articles = articleIds))
        }
        render={(o) => `${o.title} (${o.user_name})`}
      />
      <TextSub>最大{RELATED_ARTICLES_LIMIT}件まで選べます。</TextSub>
    </div>
  );
};
