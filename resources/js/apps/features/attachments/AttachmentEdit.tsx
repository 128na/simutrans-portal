import { AttachmentTable } from "./AttachmentTable";

type Props = {
  attachments: Attachment[];
  selected: number | null;
  attachmentableId: number | null;
  onClick?: (attachmentId: number | null) => void;
};

export const AttachmentEdit = ({
  attachments,
  attachmentableId,
  selected,
  onClick,
}: Props) => {
  return (
    <AttachmentTable
      attachments={attachments}
      limit={15}
      selected={selected}
      attachmentableId={attachmentableId}
      attachmentableType="Article"
      onSelectAttachment={(a) => onClick?.(a?.id ?? null)}
    />
  );
};
