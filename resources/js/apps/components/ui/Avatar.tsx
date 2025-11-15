type Props = {
  attachmentId: number | null;
  attachments: AttachmentEdit.Attachment[];
  defaultUrl?: string;
};

export const Avatar = ({
  attachmentId,
  attachments,
  defaultUrl = "/storage/default/avatar.png",
}: Props) => {
  const attachment = attachments.find((a) => a.id === attachmentId);
  const url = attachment ? attachment.thumbnail : defaultUrl;

  return <img className="w-10 h-10 rounded-full bg-gray-50" src={url} />;
};
