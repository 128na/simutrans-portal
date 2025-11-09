import { Upload } from "@/apps/components/form/Upload";
import { Accordion } from "@/apps/components/ui/Accordion";
import Input from "@/apps/components/ui/Input";
import Label from "@/apps/components/ui/Label";
import TextBadge from "@/apps/components/ui/TextBadge";
import { AttachmentEdit } from "../../attachments/AttachmentEdit";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { Image } from "@/apps/components/ui/Image";
import TextSub from "@/apps/components/ui/TextSub";

export const CommonForm = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const updateContents = useArticleEditor((s) => s.updateContents);

  const attachments = useArticleEditor((s) => s.attachments);

  return (
    <>
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
      <TextSub>
        {(article.contents.thumbnail &&
          attachments.find((a) => a.id === article.contents.thumbnail)
            ?.original_name) ??
          "未選択"}
      </TextSub>

      <Upload
        accept="image/*"
        onUploaded={(a) => {
          useArticleEditor.setState((state) => {
            // アップロードした画像を同時にセットする
            state.attachments.unshift(a);
            state.article.contents.thumbnail = a.id;
          });
        }}
      >
        画像をアップロード
      </Upload>
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
    </>
  );
};
