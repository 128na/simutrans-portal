import { Upload } from "@/components/form/Upload";
import Label from "@/components/ui/Label";
import { ModalFull } from "@/components/ui/ModalFull";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { useAxiosError } from "@/hooks/useAxiosError";

type Props = {
  section: ArticleContent.Section.Image;
  articleId: number | null;
  idx: number;
  onUploaded: (a: Attachment.MypageEdit) => void;
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
          <TextError>
            {getError(`article.contents.sections.${idx}.id`)}
          </TextError>
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
          <ModalFull
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
    </>
  );
};
