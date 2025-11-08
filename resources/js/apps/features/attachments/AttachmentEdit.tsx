import { AttachmentTable } from "./AttachmentTable";

type Props = {
  attachments: Attachment[];
  selected: number | null;
  attachmentableId: number | null;
  onSelectAttachment?: (attachmentId: number | null) => void;
  onAddAttachment?: (attachment: Attachment) => void;
};

export const AttachmentEdit = ({
  attachments,
  attachmentableId,
  selected,
  onSelectAttachment,
}: Props) => {
  return (
    <AttachmentTable
      attachments={attachments}
      limit={15}
      selected={selected}
      attachmentableId={attachmentableId}
      attachmentableType="Article"
      onSelectAttachment={(a) => onSelectAttachment?.(a?.id ?? null)}
    />
  );
};
