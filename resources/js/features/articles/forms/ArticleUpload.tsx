import { AttachmentField } from "@/features/attachments/AttachmentField";
import { useArticleEditor } from "@/hooks/useArticleEditor";

type Props = {
  kind: "file" | "image";
  selected: number | null;
  onUploaded: (attachment: Attachment.MypageEdit) => void;
  onSelectAttachment: (attachmentId: number | null) => void;
};

/**
 * 記事編集画面の添付ファイル・画像欄。アップロード中の件数を記事エディタに数え、保存を止められるようにする。
 */
export const ArticleUpload = ({
  kind,
  selected,
  onUploaded,
  onSelectAttachment,
}: Props) => {
  const articleId = useArticleEditor((s) => s.article.id);
  const attachments = useArticleEditor((s) => s.attachments);

  return (
    <AttachmentField
      kind={kind}
      attachments={attachments}
      selected={selected}
      attachmentableId={articleId}
      onUploaded={onUploaded}
      onSelectAttachment={onSelectAttachment}
      onChangeAttachments={(attachments) => {
        useArticleEditor.setState((state) => {
          state.attachments = attachments;
        });
      }}
      onUploadingChange={(uploading) =>
        useArticleEditor.setState((state) => {
          state.uploadsInProgress = Math.max(
            0,
            state.uploadsInProgress + (uploading ? 1 : -1)
          );
        })
      }
    />
  );
};
