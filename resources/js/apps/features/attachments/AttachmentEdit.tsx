import { AttachmentTable } from "./AttachmentTable";

type Props = {
  attachments: Attachment[];
  selected: number | null;
  attachmentableId: number | null;
  types?: AttachmentType[];
  onSelectAttachment?: (attachmentId: number | null) => void;
  onAddAttachment?: (attachment: Attachment) => void;
};

export const AttachmentEdit = ({
  attachments,
  attachmentableId,
  selected,
  types = ["image", "file", "video", "text"],
  onSelectAttachment,
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
    />
  );
};
