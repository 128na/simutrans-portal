import { AttachmentTable } from "./AttachmentTable";

type Props = {
  attachments: Attachment.MypageEdit[];
  onChangeAttachments?: (attachments: Attachment.MypageEdit[]) => void;
};

export const AttachmentManage = ({
  attachments,
  onChangeAttachments,
}: Props) => {
  return (
    <AttachmentTable
      attachments={attachments}
      limit={15}
      selected={null}
      attachmentableId={null}
      attachmentableType={null}
      types={["image", "file", "video", "text"]}
      onChangeAttachments={onChangeAttachments}
    />
  );
};
