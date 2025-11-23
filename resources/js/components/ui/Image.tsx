import { twMerge } from "tailwind-merge";

export const Image = ({
  attachmentId,
  attachments,
  defaultUrl = "/storage/default/image.png",
  openFullSize = false,
  className,
}: Ui.ImageProps) => {
  const attachment = attachments.find((a) => a.id === attachmentId);
  const url = attachment ? attachment.thumbnail : defaultUrl;

  const img = (
    <img
      className={twMerge(
        className ??
          "w-80 h-45 object-cover rounded-lg shadow-lg min-w-[64px] min-h-[36px]",
      )}
      src={url}
    />
  );

  return openFullSize ? (
    <a href={attachment?.url} target="_blank" rel="noopener noreferrer">
      {img}
    </a>
  ) : (
    img
  );
};
