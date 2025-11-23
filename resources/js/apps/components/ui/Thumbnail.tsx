import { Image } from "./Image";

export const Thumbnail = (props: Ui.ImageProps) => {
  return (
    <Image className="mt-6 mb-12 w-max-full rounded-lg shadow-md" {...props} />
  );
};
