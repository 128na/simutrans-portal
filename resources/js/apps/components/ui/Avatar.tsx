import { Image } from "./Image";

type Props = {
  attachmentId: number | null;
  attachments: Attachment.MypageEdit[] | Attachment.Show[];
  defaultUrl?: string;
};

export const Avatar = ({
  defaultUrl = "/storage/default/avatar.png",
  ...props
}: Props) => {
  return (
    <Image
      className="w-10 h-10 rounded-full bg-gray-50"
      defaultUrl={defaultUrl}
      {...props}
    />
  );
};
