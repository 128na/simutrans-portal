import { ProfileService } from "@/types/utils";
import { twMerge } from "tailwind-merge";

type Props = {
  service: ProfileService;
} & React.HTMLAttributes<HTMLImageElement>;

export const ProfileIcon = ({ service, className, ...props }: Props) => {
  return (
    <img
      src={service.src}
      alt={service.service}
      title={service.service}
      className={twMerge("inline-block h-[1em] align-text-bottom", className)}
      {...props}
    />
  );
};
