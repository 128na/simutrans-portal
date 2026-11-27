import { Image } from "./Image";

export const Thumbnail = (props: Ui.ImageProps) => {
  return <Image className="max-w-full rounded-lg shadow-md" {...props} />;
};
