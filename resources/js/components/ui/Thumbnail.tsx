import { Image } from "./Image";

export const Thumbnail = (props: Ui.ImageProps) => {
  return <Image className="w-max-full rounded-lg shadow-md" {...props} />;
};
