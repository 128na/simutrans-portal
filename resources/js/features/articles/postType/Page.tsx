import { SelectCategories } from "../components/SelectCategories";
import { RelatedArticles } from "../components/RelatedArticles";
import { Accordion } from "@/components/ui/Accordion";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { SectionForm } from "../forms/SectionForm";
import { useAxiosError } from "@/hooks/useAxiosError";

export const Page = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const categories = useArticleEditor((s) => s.categories);
  const isAdmin = useArticleEditor((s) => s.user.role === "admin");
  const { hasErrorTree } = useAxiosError();

  return (
    <>
      <SectionForm />
      <SelectCategories
        categories={categories}
        selected={article.categories}
        showAdmin={isAdmin}
        only={["page"]}
        onChange={(categoryIds) =>
          update((draft) => (draft.categories = categoryIds))
        }
      />
      <Accordion
        title="その他の項目"
        defaultOpen={!!article.articles.length}
        forceOpen={hasErrorTree("article.articles")}
      >
        <div className="grid gap-4">
          <RelatedArticles />
        </div>
      </Accordion>
    </>
  );
};
