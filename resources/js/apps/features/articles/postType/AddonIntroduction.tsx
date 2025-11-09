import Input from "@/apps/components/ui/Input";
import Textarea from "@/apps/components/ui/Textarea";
import { SelectCategories } from "../SelectCategories";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";
import Label from "@/apps/components/ui/Label";
import { Accordion } from "@/apps/components/ui/Accordion";
import { TagEdit } from "../../tags/TagEdit";
import TextBadge from "@/apps/components/ui/TextBadge";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { CommonForm } from "../form/CommonForm";
import { StatusForm } from "../form/StatusForm";

export const AddonIntroduction = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const contents = article.contents as ContentAddonIntroduction;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const tags = useArticleEditor((s) => s.tags);
  const updateTags = useArticleEditor((s) => s.updateTags);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  return (
    <div className="grid gap-4">
      <CommonForm />
      <Textarea
        labelClassName="font-medium"
        className="font-normal"
        value={contents.description || ""}
        rows={9}
        onChange={(e) =>
          updateContents<ContentAddonIntroduction>(
            (draft) => (draft.description = e.target.value),
          )
        }
      >
        <TextBadge color="red">必須</TextBadge>
        説明
      </Textarea>
      <Input
        labelClassName="font-medium"
        className="font-normal"
        type="url"
        value={contents.link || ""}
        onChange={(e) =>
          updateContents<ContentAddonIntroduction>(
            (draft) => (draft.link = e.target.value),
          )
        }
      >
        <TextBadge color="red">必須</TextBadge>
        リンク先
      </Input>
      <SelectCategories
        typeClassName="font-medium"
        className="font-normal"
        categories={categories}
        selected={article.categories}
        only={["pak", "addon", "pak128_position", "license", "double_slope"]}
        onChange={(categoryIds) =>
          update((draft) => (draft.categories = categoryIds))
        }
      />
      <Accordion
        title="その他の項目"
        defaultOpen={
          !!(
            contents.thanks ||
            contents.license ||
            article.tags.length ||
            article.articles.length
          )
        }
      >
        <div className="pl-4 grid gap-4">
          <Input
            labelClassName="font-medium"
            className="font-normal"
            value={contents.author || ""}
            onChange={(e) =>
              updateContents<ContentAddonIntroduction>(
                (draft) => (draft.author = e.target.value),
              )
            }
          >
            作者
          </Input>
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
            value={contents.thanks || ""}
            rows={3}
            onChange={(e) =>
              updateContents<ContentAddonIntroduction>(
                (draft) => (draft.thanks = e.target.value),
              )
            }
          >
            謝辞
          </Textarea>
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
            value={contents.license || ""}
            rows={3}
            onChange={(e) =>
              updateContents<ContentAddonIntroduction>(
                (draft) => (draft.license = e.target.value),
              )
            }
          >
            ライセンス
          </Textarea>
          <Label className="font-medium">
            タグ
            <SelectableSearch
              className="font-normal"
              labelKey="name"
              options={tags}
              selectedIds={article.tags}
              onChange={(tagIds) => update((draft) => (draft.tags = tagIds))}
            />
          </Label>
          <Accordion title="タグの作成・編集">
            <div className="pl-4 grid gap-4">
              <TagEdit
                tags={tags}
                onChangeTags={(tags) => {
                  updateTags(tags);
                }}
              />
            </div>
          </Accordion>

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
