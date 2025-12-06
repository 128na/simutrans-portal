import { ProfileService } from "@/types/utils";

type Props = {
  service: ProfileService;
};

export const ProfileIcon = ({ service }: Props) => {
  return (
    <img
      src={service.src}
      alt={service.service}
      title={service.service}
      className="inline-block h-[1em] align-text-bottom"
    />
  );
};
