import { ProfileService } from "@/types/utils";

export const getService = (url: string) => {
  const starts = (prefix: string) => url.startsWith(prefix);
  const services = [
    {
      service: "Twitter",
      match: starts("https://twitter.com/") || starts("https://x.com/"),
      src: "/storage/social/twitter.svg",
    },
    {
      service: "Misskey",
      match: starts("https://misskey.io/"),
      src: "/storage/social/misskey.svg",
    },
    {
      service: "Bluesky",
      match: starts("https://bsky.app/"),
      src: "/storage/social/bluesky.svg",
    },
    {
      service: "Discord",
      match: starts("https://discord.gg/") || starts("https://discord.com/"),
      src: "/storage/social/discord.svg",
    },
    {
      service: "YouTube",
      match: starts("https://www.youtube.com/"),
      src: "/storage/social/youtube.png",
    },
    {
      service: "Niconico",
      match: starts("https://www.nicovideo.jp/"),
      src: "/storage/social/niconico.png",
    },
    {
      service: "GitHub",
      match: starts("https://github.com/"),
      src: "/storage/social/github.svg",
    },
  ] as ProfileService[];
  return services.find((it) => it.match);
};
