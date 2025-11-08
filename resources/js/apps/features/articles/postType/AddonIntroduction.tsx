import Input from "@/apps/components/ui/Input";
import Textarea from "@/apps/components/ui/Textarea";
import { SelectCategories } from "../SelectCategories";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";
import Label from "@/apps/components/ui/Label";
import { Accordion } from "@/apps/components/ui/Accordion";
import Select from "@/apps/components/ui/Select";
import { t } from "@/lang/translate";
import { Image } from "../../../components/ui/Image";
import { TagEdit } from "../../tags/TagEdit";
import { AttachmentEdit } from "../../attachments/AttachmentEdit";
import TextBadge from "@/apps/components/ui/TextBadge";
import { addHours, format } from "date-fns";
import { Upload } from "@/apps/components/form/Upload";
import { useArticleEditor } from "@/apps/state/useArticleEditor";

export const AddonIntroduction = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const contents = article.contents as ContentAddonIntroduction;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const attachments = useArticleEditor((s) => s.attachments);

  const tags = useArticleEditor((s) => s.tags);
  const updateTags = useArticleEditor((s) => s.updateTags);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  const options = {
    publish: t("statuses.publish"),
    reservation: t("statuses.reservation"),
    draft: t("statuses.draft"),
    private: t("statuses.private"),
    trash: t("statuses.trash"),
  };

  return (
    <div className="grid gap-4">
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={article.title || ""}
        onChange={(e) => update((draft) => (draft.title = e.target.value))}
      >
        <TextBadge color="red">必須</TextBadge>
        タイトル
      </Input>
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={article.slug || ""}
        onChange={(e) => update((draft) => (draft.slug = e.target.value))}
      >
        <TextBadge color="red">必須</TextBadge>
        記事URL
      </Input>
      <Label className="font-medium">
        サムネイル
        <Image
          attachmentId={article.contents.thumbnail}
          attachments={attachments}
        />
      </Label>
      <Upload
        onUploaded={(a) => {
          useArticleEditor.setState((state) => {
            // アップロードした画像を同時にセットする
            state.attachments.unshift(a);
            state.article.contents.thumbnail = a.id;
          });
        }}
      />
      <Accordion title="アップロード済みの画像から選択する">
        <div className="pl-4 grid gap-4">
          <AttachmentEdit
            attachments={attachments}
            attachmentableId={article.id}
            selected={article.contents.thumbnail}
            onSelectAttachment={(attachmentId) =>
              updateContents((draft) => (draft.thumbnail = attachmentId))
            }
          />
        </div>
      </Accordion>
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
      <Select
        options={options}
        value={article.status}
        onChange={(e) =>
          update((draft) => (draft.status = e.target.value as Status))
        }
      >
        ステータス
      </Select>
      {article.status === "reservation" && (
        <Input
          type="datetime-local"
          value={article.published_at ?? ""}
          min={format(addHours(new Date(), 1), "yyyy-MM-dd'T'HH:mm")}
          onChange={(e) =>
            update((draft) => (draft.published_at = e.target.value))
          }
        >
          予約日時
        </Input>
      )}
    </div>
  );
};
