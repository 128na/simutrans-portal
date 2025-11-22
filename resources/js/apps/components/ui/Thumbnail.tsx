import { Image } from "./Image";

type Props = {
  attachmentId: number | null;
  attachments: AttachmentEdit.AttachmentShowable[];
  defaultUrl?: string;
};

export const Thumbnail = (props: Props) => {
  return (
    <Image className="mt-6 mb-12 w-max-full rounded-lg shadow-md" {...props} />
  );
};
