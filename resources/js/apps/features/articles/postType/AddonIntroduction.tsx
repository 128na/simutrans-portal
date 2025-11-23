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
import Checkbox from "@/apps/components/ui/Checkbox";
import { useAxiosError } from "@/apps/state/useAxiosError";
import TextError from "@/apps/components/ui/TextError";
import { ModalFull } from "@/apps/components/ui/ModalFull";

export const AddonIntroduction = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const contents = article.contents as ArticleContent.AddonIntroduction;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const tags = useArticleEditor((s) => s.tags);
  const updateTags = useArticleEditor((s) => s.updateTags);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  const { getError } = useAxiosError();

  return (
    <div className="grid gap-4">
      <CommonForm />
      <Textarea
        labelClassName="font-medium"
        className="font-normal"
        value={contents.description || ""}
        rows={9}
        onChange={(e) =>
          updateContents<ArticleContent.AddonIntroduction>(
            (draft) => (draft.description = e.target.value),
          )
        }
      >
        <TextBadge className="bg-red-500">必須</TextBadge>
        説明
        <TextError>{getError("article.contents.description")}</TextError>
      </Textarea>
      <Input
        labelClassName="font-medium"
        className="font-normal"
        type="url"
        value={contents.link || ""}
        onChange={(e) =>
          updateContents<ArticleContent.AddonIntroduction>(
            (draft) => (draft.link = e.target.value),
          )
        }
      >
        <TextBadge className="bg-red-500">必須</TextBadge>
        リンク先
        <TextError>{getError("article.contents.link")}</TextError>
      </Input>
      <Label>
        <div className="font-medium">掲載許可</div>
        <Checkbox
          checked={contents.agreement}
          onChange={() =>
            updateContents<ArticleContent.AddonIntroduction>(
              (draft) => (draft.agreement = !draft.agreement),
            )
          }
        >
          取得済み
          <TextError>{getError("article.contents.agreement")}</TextError>
        </Checkbox>
      </Label>
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
              updateContents<ArticleContent.AddonIntroduction>(
                (draft) => (draft.author = e.target.value),
              )
            }
          >
            作者
            <TextError>{getError("article.contents.author")}</TextError>
          </Input>
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
            value={contents.thanks || ""}
            rows={3}
            onChange={(e) =>
              updateContents<ArticleContent.AddonIntroduction>(
                (draft) => (draft.thanks = e.target.value),
              )
            }
          >
            謝辞
            <TextError>{getError("article.contents.thanks")}</TextError>
          </Textarea>
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
            value={contents.license || ""}
            rows={3}
            onChange={(e) =>
              updateContents<ArticleContent.AddonIntroduction>(
                (draft) => (draft.license = e.target.value),
              )
            }
          >
            ライセンス
            <TextError>{getError("article.contents.license")}</TextError>
          </Textarea>
          <Label>
            <div className="font-medium">リンク切れチェック</div>
            <TextError>
              {getError("article.contents.exclude_link_check")}
            </TextError>
            <Checkbox
              checked={contents.exclude_link_check}
              onChange={() =>
                updateContents<ArticleContent.AddonIntroduction>(
                  (draft) =>
                    (draft.exclude_link_check = !draft.exclude_link_check),
                )
              }
            >
              有効にする
            </Checkbox>
          </Label>
          <div>
            <Label>
              <div className="font-medium">タグ</div>
              <SelectableSearch
                className="font-normal"
                labelKey="name"
                options={tags}
                selectedIds={article.tags}
                onChange={(tagIds) => update((draft) => (draft.tags = tagIds))}
              />
            </Label>
            <ModalFull buttonTitle="タグの作成・編集" title="タグの作成・編集">
              <TagEdit
                tags={tags}
                onChangeTags={(tags) => {
                  updateTags(tags);
                }}
              />
            </ModalFull>
          </div>

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
