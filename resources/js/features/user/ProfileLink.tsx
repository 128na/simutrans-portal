import LinkExternal from "@/components/ui/LinkExternal";
import { ProfileIcon } from "./ProfileIcon";
import { getService } from "./profileUtil";

type Props = {
  url: string;
  preview: boolean;
};

export const ProfileLink = ({ url, preview }: Props) => {
  const found = getService(url);

  if (found) {
    return (
      <span className="mr-1">
        <a href={preview ? "#" : url} target="_blank" rel="noopener noreferrer">
          <ProfileIcon service={found} />
        </a>
      </span>
    );
  }

  // デフォルト: 外部リンクとしてホスト名を表示
  const host = (() => {
    try {
      return new URL(url).host;
    } catch {
      return url;
    }
  })();

  return <LinkExternal href={preview ? "#" : url}>{host}</LinkExternal>;
};
