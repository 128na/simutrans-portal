import { Upload } from "@/apps/components/form/Upload";
import { Image } from "@/apps/components/ui/Image";
import Label from "@/apps/components/ui/Label";
import { Modal } from "@/apps/components/ui/Modal";
import TextError from "@/apps/components/ui/TextError";
import TextSub from "@/apps/components/ui/TextSub";
import { AttachmentEdit } from "@/apps/features/attachments/AttachmentEdit";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { useAxiosError } from "@/apps/state/useAxiosError";

type Props = {
  section: SectionImage;
  articleId: number | null;
  idx: number;
  onUploaded: (a: Attachment) => void;
  onSelectAttachment: (id: number | null) => void;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const SectionImage = ({
  section,
  articleId,
  idx,
  onUploaded,
  onSelectAttachment,
}: Props) => {
  const attachments = useArticleEditor((s) => s.attachments);
  const { getError } = useAxiosError();

  return (
    <>
      <div>
        <Label className="font-medium">
          画像
          <TextError className="mb-2">
            {getError(`article.contents.sections.${idx}.id`)}
          </TextError>
          <Image attachmentId={section.id} attachments={attachments} />
        </Label>
        <TextSub>
          {(section.id &&
            attachments.find((a) => a.id === section.id)?.original_name) ??
            "未選択"}
        </TextSub>
        <div className="space-x-2">
          <Upload accept="image/*" onUploaded={onUploaded}>
            画像をアップロード
          </Upload>
          <Modal
            buttonTitle="アップロード済みの画像から選択"
            title="画像を選択"
          >
            {({ close }) => (
              <AttachmentEdit
                attachments={attachments}
                attachmentableId={articleId}
                selected={section.id}
                types={["image"]}
                onSelectAttachment={(attachmentId) => {
                  onSelectAttachment(attachmentId);
                  close();
                }}
              />
            )}
          </Modal>
        </div>
      </div>
    </>
  );
};
