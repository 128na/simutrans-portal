import Input from "@/components/ui/Input";
import Textarea from "@/components/ui/Textarea";
import { SelectCategories } from "../components/SelectCategories";
import { SelectableSearch } from "@/components/form/SelectableSearch";
import Label from "@/components/ui/Label";
import { Accordion } from "@/components/ui/Accordion";
import { TagEdit } from "../../tags/TagEdit";
import TextBadge from "@/components/ui/TextBadge";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { CommonForm } from "../forms/CommonForm";
import { StatusForm } from "../forms/StatusForm";
import { Upload } from "@/components/form/Upload";
import TextSub from "@/components/ui/TextSub";
import { useAxiosError } from "@/hooks/useAxiosError";
import TextError from "@/components/ui/TextError";
import { ModalFull } from "@/components/ui/ModalFull";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import {
  getCategories,
  getReadmeText,
} from "@/features/attachments/fileInfoTool";
import { t } from "@/utils/translate";
import ButtonOutline from "@/components/ui/ButtonOutline";

export const AddonPost = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const contents = article.contents as ArticleContent.AddonPost;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const tags = useArticleEditor((s) => s.tags);
  const updateTags = useArticleEditor((s) => s.updateTags);

  const categories = useArticleEditor((s) => s.categories);
  const relationalArticles = useArticleEditor((s) => s.relationalArticles);

  const attachments = useArticleEditor((s) => s.attachments);

  const file = attachments.find((a) => a.id === contents.file);

  const { getError } = useAxiosError();

  const hasReadme = !!file?.fileInfo?.data.readmes;
  const hasPakMetadata = !!file?.fileInfo?.data.paks_metadata;

  const handleFillFromFile = () => {
    if (hasReadme) {
      const description = getReadmeText(file!.fileInfo!.data!.readmes!);

      if (
        description &&
        confirm("以下の内容を説明欄に追記します。\n" + description)
      ) {
        updateContents<ArticleContent.AddonPost>(
          (draft) => (draft.description += description)
        );
      }
    }

    if (hasPakMetadata) {
      const selectedCategories = getCategories(
        file!.fileInfo!.data!.paks_metadata!,
        categories
      );

      if (
        selectedCategories.length &&
        confirm(
          "以下のカテゴリを選択します。\n" +
            selectedCategories
              .map((c) => t(`category.${c.type}.${c.slug}`))
              .join(", ")
        )
      ) {
        update(
          (draft) =>
            (draft.categories = Array.from(
              new Set([
                ...draft.categories,
                ...selectedCategories.map((c) => c.id),
              ])
            ))
        );
      }
    }
  };

  return (
    <div className="grid gap-4">
      <CommonForm />

      <div>
        <Label className="font-medium">
          ファイル
          <TextError>{getError("article.contents.file")}</TextError>
        </Label>
        <TextSub>{(contents.file && file?.original_name) ?? "未選択"}</TextSub>
        <div className="space-x-2">
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
          <ModalFull
            buttonTitle="アップロード済みのファイルから選択"
            title="ファイルを選択"
          >
            {({ close }) => (
              <AttachmentEdit
                attachments={attachments}
                attachmentableId={article.id}
                selected={contents.file}
                types={["file"]}
                onSelectAttachment={(attachmentId) => {
                  updateContents<ArticleContent.AddonPost>(
                    (draft) => (draft.file = attachmentId)
                  );
                  close();
                }}
                onChangeAttachments={(attachments) => {
                  useArticleEditor.setState((state) => {
                    state.attachments = attachments;
                  });
                }}
              />
            )}
          </ModalFull>
        </div>
      </div>
      <div>
        <ButtonOutline
          disabled={!hasReadme && !hasPakMetadata}
          onClick={handleFillFromFile}
        >
          ファイルの内容から項目を入力する
        </ButtonOutline>
      </div>

      <div>
        <Textarea
          labelClassName="font-medium"
          className="font-normal"
          value={contents.description || ""}
          rows={9}
          onChange={(e) =>
            updateContents<ArticleContent.AddonPost>(
              (draft) => (draft.description = e.target.value)
            )
          }
        >
          <TextBadge className="bg-red-500">必須</TextBadge>
          説明
          <TextError>{getError("article.contents.description")}</TextError>
        </Textarea>
      </div>

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
              updateContents<ArticleContent.AddonPost>(
                (draft) => (draft.author = e.target.value)
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
              updateContents<ArticleContent.AddonPost>(
                (draft) => (draft.thanks = e.target.value)
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
              updateContents<ArticleContent.AddonPost>(
                (draft) => (draft.license = e.target.value)
              )
            }
          >
            ライセンス
            <TextError>{getError("article.contents.license")}</TextError>
          </Textarea>

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
