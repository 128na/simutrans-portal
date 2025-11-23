import { twMerge } from "tailwind-merge";

type Props = {
  attachmentId: number | null;
  attachments: Attachment.MypageEdit[] | Attachment.Show[];
  defaultUrl?: string;
  className?: string;
};

export const Image = ({
  attachmentId,
  attachments,
  defaultUrl = "/storage/default/image.png",
  className,
}: Props) => {
  const attachment = attachments.find((a) => a.id === attachmentId);
  const url = attachment ? attachment.thumbnail : defaultUrl;

  return (
    <img
      className={twMerge(
        className ??
          "w-80 h-45 object-cover rounded-lg shadow-lg min-w-[64px] min-h-[36px]",
      )}
      src={url}
    />
  );
};
