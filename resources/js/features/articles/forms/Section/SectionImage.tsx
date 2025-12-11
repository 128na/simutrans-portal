import { Upload } from "@/components/form/Upload";
import { ModalFull } from "@/components/ui/ModalFull";
import TextError from "@/components/ui/TextError";
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
        <TextError>{getError(`article.contents.sections.${idx}.id`)}</TextError>
        <Upload
          className="w-full mb-4"
          accept="image/*"
          onUploaded={onUploaded}
        />
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
    </>
  );
};
