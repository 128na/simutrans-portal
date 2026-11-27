import { twMerge } from "tailwind-merge";

export const Image = ({
  attachmentId,
  attachments,
  defaultUrl = "/storage/default/image.png",
  openFullSize = false,
  original = false,
  className,
}: Ui.ImageProps) => {
  const attachment = attachments.find((a) => a.id === attachmentId);
  // original は元画像を出す指定。元画像の URL が無い添付はサムネイルにする
  const url = attachment
    ? (original && attachment.url) || attachment.thumbnail
    : defaultUrl;

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
    <a
      href={attachment?.url}
      target="_blank"
      rel="noopener noreferrer"
      // 元画像を出すときは、画像の横の余白までリンクにならないようにする
      className={original ? "w-fit max-w-full" : undefined}
    >
      {img}
    </a>
  );
};
