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
import { Upload } from "@/apps/components/form/Upload";
import { AttachmentEdit } from "../../attachments/AttachmentEdit";
import TextSub from "@/apps/components/ui/TextSub";
import { useAxiosError } from "@/apps/state/useAxiosError";
import TextError from "@/apps/components/ui/TextError";

export const AddonPost = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const contents = article.contents as ContentAddonPost;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const tags = useArticleEditor((s) => s.tags);
  const updateTags = useArticleEditor((s) => s.updateTags);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  const attachments = useArticleEditor((s) => s.attachments);

  const { getError } = useAxiosError();

  return (
    <div className="grid gap-4">
      <CommonForm />
      <Label className="font-medium">ファイル</Label>
      <TextError className="mb-2">
        {getError("article.contents.file")}
      </TextError>
      <TextSub>
        {(contents.file &&
          attachments.find((a) => a.id === contents.file)?.original_name) ??
          "未選択"}
      </TextSub>
      <Upload
        onUploaded={(a) => {
          useArticleEditor.setState((state) => {
            state.attachments.unshift(a);
            if ("file" in state.article.contents) {
              state.article.contents.file = a.id;
            }
          });
        }}
      >
        ファイルをアップロード
      </Upload>
      <Accordion title="アップロード済みのファイルから選択する">
        <div className="pl-4 grid gap-4">
          <AttachmentEdit
            attachments={attachments}
            attachmentableId={article.id}
            selected={contents.file}
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
          updateContents<ContentAddonPost>(
            (draft) => (draft.description = e.target.value),
          )
        }
      >
        <TextBadge color="red">必須</TextBadge>
        説明
        <TextError className="mb-2">
          {getError("article.contents.description")}
        </TextError>
      </Textarea>

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
              updateContents<ContentAddonPost>(
                (draft) => (draft.author = e.target.value),
              )
            }
          >
            作者
            <TextError className="mb-2">
              {getError("article.contents.author")}
            </TextError>
          </Input>
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
            value={contents.thanks || ""}
            rows={3}
            onChange={(e) =>
              updateContents<ContentAddonPost>(
                (draft) => (draft.thanks = e.target.value),
              )
            }
          >
            謝辞
            <TextError className="mb-2">
              {getError("article.contents.thanks")}
            </TextError>
          </Textarea>
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
            value={contents.license || ""}
            rows={3}
            onChange={(e) =>
              updateContents<ContentAddonPost>(
                (draft) => (draft.license = e.target.value),
              )
            }
          >
            ライセンス
            <TextError className="mb-2">
              {getError("article.contents.license")}
            </TextError>
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
