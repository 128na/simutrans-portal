import LinkExternal from "@/apps/components/ui/LinkExternal";

type Props = {
  url: string;
  preview: boolean;
};

export const ProfileLink = ({ url, preview }: Props) => {
  const starts = (prefix: string) => url.startsWith(prefix);

  const items = [
    {
      match: starts("https://twitter.com/") || starts("https://x.com/"),
      src: "/storage/social/twitter.svg",
      alt: "Twitter",
    },
    {
      match: starts("https://misskey.io/"),
      src: "/storage/social/misskey.svg",
      alt: "Misskey",
    },
    {
      match: starts("https://bsky.app/"),
      src: "/storage/social/bluesky.svg",
      alt: "Bluesky",
    },
    {
      match: starts("https://discord.gg/") || starts("https://discord.com/"),
      src: "/storage/social/discord.svg",
      alt: "Discord",
    },
    {
      match: starts("https://www.youtube.com/"),
      src: "/storage/social/youtube.png",
      alt: "YouTube",
    },
    {
      match: starts("https://www.nicovideo.jp/"),
      src: "/storage/social/niconico.png",
      alt: "Niconico",
    },
    {
      match: starts("https://github.com/"),
      src: "/storage/social/github.svg",
      alt: "GitHub",
    },
  ];

  // マッチするアイテムを探す
  const found = items.find((it) => it.match);

  if (found) {
    return (
      <span className="mr-1">
        <a href={preview ? "#" : url} target="_blank" rel="noopener noreferrer">
          <img
            src={found.src}
            alt={found.alt}
            title={found.alt}
            className="inline-block h-[1em] align-text-bottom"
          />
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
