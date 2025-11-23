import { SelectCategories } from "../components/SelectCategories";
import { SelectableSearch } from "@/components/form/SelectableSearch";
import Label from "@/components/ui/Label";
import { Accordion } from "@/components/ui/Accordion";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { CommonForm } from "../forms/CommonForm";
import { StatusForm } from "../forms/StatusForm";
import { SectionForm } from "../forms/SectionForm";

export const Page = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  return (
    <div className="grid gap-4">
      <CommonForm />
      <SectionForm />
      <SelectCategories
        typeClassName="font-medium"
        className="font-normal"
        categories={categories}
        selected={article.categories}
        only={["page"]}
        onChange={(categoryIds) =>
          update((draft) => (draft.categories = categoryIds))
        }
      />
      <Accordion title="その他の項目" defaultOpen={!!article.articles.length}>
        <div className="pl-4 grid gap-4">
          <Label className="font-medium">
            関連記事
            <SelectableSearch
              className="font-normal"
              labelKey="title"
              options={relationalArticles}
              selectedIds={article.articles}
              onChange={(articleIds) =>
                update((draft) => (draft.articles = articleIds))
              }
              render={(o) => `${o.title} (${o.user_name})`}
            />
          </Label>
        </div>
      </Accordion>
      <StatusForm />
    </div>
  );
};
