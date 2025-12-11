import { SelectCategories } from "../components/SelectCategories";
import { SelectableSearch } from "@/components/form/SelectableSearch";
import { Accordion } from "@/components/ui/Accordion";
import { TagEdit } from "../../tags/TagEdit";
import TextBadge from "@/components/ui/TextBadge";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { useAxiosError } from "@/hooks/useAxiosError";
import TextError from "@/components/ui/TextError";
import { ModalFull } from "@/components/ui/ModalFull";
import { FormCaption } from "@/components/ui/FormCaption";
import TextSub from "@/components/ui/TextSub";
import Textarea from "@/components/ui/Textarea";
import Input from "@/components/ui/Input";
import Checkbox from "@/components/ui/Checkbox";

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
    <>
      <div>
        <FormCaption>
          <TextBadge variant="danger">必須</TextBadge>
          説明
        </FormCaption>
        <TextError>{getError("article.contents.description")}</TextError>
        <Textarea
          className="w-full"
          value={contents.description || ""}
          required
          maxLength={2048}
          rows={9}
          onChange={(e) =>
            updateContents<ArticleContent.AddonIntroduction>(
              (draft) => (draft.description = e.target.value)
            )
          }
        />
      </div>

      <div>
        <FormCaption>
          <TextBadge variant="danger">必須</TextBadge>
          リンク先
        </FormCaption>
        <TextError>{getError("article.contents.link")}</TextError>
        <Input
          type="url"
          className="w-full"
          required
          maxLength={255}
          value={contents.link || ""}
          onChange={(e) =>
            updateContents<ArticleContent.AddonIntroduction>(
              (draft) => (draft.link = e.target.value)
            )
          }
        />
      </div>

      <div>
        <FormCaption>掲載許可</FormCaption>
        <TextError>{getError("article.contents.agreement")}</TextError>
        <Checkbox
          checked={contents.agreement}
          onChange={() =>
            updateContents<ArticleContent.AddonIntroduction>(
              (draft) => (draft.agreement = !draft.agreement)
            )
          }
        >
          取得済み
        </Checkbox>
      </div>

      <SelectCategories
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
        <div className="grid gap-4">
          <div>
            <FormCaption>作者</FormCaption>
            <TextError>{getError("article.contents.author")}</TextError>
            <Input
              className="w-full"
              maxLength={255}
              value={contents.author || ""}
              onChange={(e) =>
                updateContents<ArticleContent.AddonIntroduction>(
                  (draft) => (draft.author = e.target.value)
                )
              }
            />
          </div>
          <div>
            <FormCaption>謝辞</FormCaption>
            <TextError>{getError("article.contents.thanks")}</TextError>
            <Textarea
              className="w-full"
              value={contents.thanks || ""}
              maxLength={2048}
              rows={3}
              onChange={(e) =>
                updateContents<ArticleContent.AddonIntroduction>(
                  (draft) => (draft.thanks = e.target.value)
                )
              }
            />
          </div>
          <div>
            <FormCaption>ライセンス</FormCaption>
            <TextError>{getError("article.contents.license")}</TextError>
            <Textarea
              className="w-full"
              maxLength={2048}
              value={contents.license || ""}
              rows={3}
              onChange={(e) =>
                updateContents<ArticleContent.AddonIntroduction>(
                  (draft) => (draft.license = e.target.value)
                )
              }
            />
          </div>
          <div>
            <FormCaption>リンク切れチェック</FormCaption>
            <TextError>
              {getError("article.contents.exclude_link_check")}
            </TextError>
            <Checkbox
              checked={contents.exclude_link_check}
              onChange={() =>
                updateContents<ArticleContent.AddonIntroduction>(
                  (draft) =>
                    (draft.exclude_link_check = !draft.exclude_link_check)
                )
              }
            >
              有効にする
            </Checkbox>
            <TextSub>
              デイリーで自動的にリンク先URLがアクセスできる状態かチェックします。3日間リンク切れ状態が続くと記事を非公開に変更し、メールで通知します。
            </TextSub>
          </div>
          <div>
            <FormCaption>タグ</FormCaption>
            <SelectableSearch
              className="mb-1"
              labelKey="name"
              options={tags}
              selectedIds={article.tags}
              onChange={(tagIds) => update((draft) => (draft.tags = tagIds))}
            />
            <ModalFull buttonTitle="タグの作成・編集" title="タグの作成・編集">
              <TagEdit
                tags={tags}
                onChangeTags={(tags) => {
                  updateTags(tags);
                }}
              />
            </ModalFull>
          </div>
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
    </>
  );
};
