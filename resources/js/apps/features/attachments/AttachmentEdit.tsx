import { AttachmentTable } from "./AttachmentTable";

type Props = {
  attachments: AttachmentEdit.Attachment[];
  selected: number | null;
  attachmentableId: number | null;
  types?: AttachmentEdit.Type[];
  onSelectAttachment?: (attachmentId: number | null) => void;
  onChangeAttachments?: (attachments: AttachmentEdit.Attachment[]) => void;
};

export const AttachmentEdit = ({
  attachments,
  attachmentableId,
  selected,
  types = ["image", "file", "video", "text"],
  onSelectAttachment,
  onChangeAttachments,
}: Props) => {
  return (
    <AttachmentTable
      attachments={attachments}
      limit={15}
      selected={selected}
      attachmentableId={attachmentableId}
      attachmentableType="Article"
      types={types}
      onSelectAttachment={(a) => onSelectAttachment?.(a?.id ?? null)}
      onChangeAttachments={onChangeAttachments}
    />
  );
};
