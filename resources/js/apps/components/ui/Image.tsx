type Props = {
  attachmentId: number | null;
  attachments: Attachment[];
  defaultUrl?: string;
};

export const Image = ({
  attachmentId,
  attachments,
  defaultUrl = "/storage/default/image.png",
}: Props) => {
  const attachment = attachments.find((a) => a.id === attachmentId);
  const url = attachment ? attachment.thumbnail : defaultUrl;

  return (
    <img className="w-80 h-45 object-cover rounded-lg shadow-lg" src={url} />
  );
};
