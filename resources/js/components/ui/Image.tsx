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
          "aspect-video w-80 object-cover rounded-lg shadow-lg min-w-32"
      )}
      src={url}
    />
  );

  if (!openFullSize) {
    return <div>{img}</div>;
  }

  return (
    <a href={attachment?.url} target="_blank" rel="noopener noreferrer">
      {img}
    </a>
  );
};
