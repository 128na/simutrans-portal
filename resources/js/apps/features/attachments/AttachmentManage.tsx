import { AttachmentTable } from "./AttachmentTable";

type Props = {
  attachments: AttachmentEdit.Attachment[];
  onChangeAttachments?: (attachments: AttachmentEdit.Attachment[]) => void;
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
