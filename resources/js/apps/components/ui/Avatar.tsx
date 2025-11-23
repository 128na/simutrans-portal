import { Image } from "./Image";

export const Avatar = ({
  defaultUrl = "/storage/default/avatar.png",
  ...props
}: Ui.ImageProps) => {
  return (
    <Image
      className="w-10 h-10 rounded-full bg-gray-50"
      defaultUrl={defaultUrl}
      {...props}
    />
  );
};
