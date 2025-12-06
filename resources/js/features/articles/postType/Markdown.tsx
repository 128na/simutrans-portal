import Textarea from "@/components/ui/Textarea";
import { SelectCategories } from "../components/SelectCategories";
import { SelectableSearch } from "@/components/form/SelectableSearch";
import { Accordion } from "@/components/ui/Accordion";
import TextBadge from "@/components/ui/TextBadge";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { CommonForm } from "../forms/CommonForm";
import { StatusForm } from "../forms/StatusForm";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";
import { FormCaption } from "@/components/ui/FormCaption";

export const Markdown = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const contents = article.contents as ArticleContent.Markdown;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  const { getError } = useAxiosError();

  return (
    <>
      <CommonForm />

      <div>
        <FormCaption>
          <TextBadge className="bg-danger">必須</TextBadge>
          本文
        </FormCaption>
        <TextError>{getError("article.contents.markdown")}</TextError>

        <Textarea
          labelClassName="font-medium"
          className="font-normal"
          value={contents.markdown || ""}
          rows={15}
          onChange={(e) =>
            updateContents<ArticleContent.Markdown>((draft) => {
              draft.markdown = e.target.value;
            })
          }
        />
      </div>

      <SelectCategories
        categories={categories}
        selected={article.categories}
        only={["page"]}
        onChange={(categoryIds) =>
          update((draft) => (draft.categories = categoryIds))
        }
      />

      <Accordion title="その他の項目" defaultOpen={!!article.articles.length}>
        <div className="grid gap-4">
          <div>
            <FormCaption>関連記事</FormCaption>
            <SelectableSearch
              labelKey="title"
              options={relationalArticles}
              selectedIds={article.articles}
              onChange={(articleIds) =>
                update((draft) => (draft.articles = articleIds))
              }
              render={(o) => `${o.title} (${o.user_name})`}
            />
          </div>
        </div>
      </Accordion>
      <StatusForm />
    </>
  );
};
