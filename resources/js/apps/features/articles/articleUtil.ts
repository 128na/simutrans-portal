import { compareAsc, parseISO } from "date-fns";
export const compareArticleValues = (a: unknown, b: unknown): number => {
  // null を末尾扱いにする
  if (a == null && b == null) return 0;
  if (a == null) return 1;
  if (b == null) return -1;

  if (typeof a === "object" || typeof b === "object") {
    const aCount = a as Count | null;
    const bCount = b as Count | null;
    return (aCount?.count ?? 0) - (bCount?.count ?? 0);
  }

  // 日付文字列
  if (typeof a === "string" && /^\d{4}-\d{2}-\d{2}/.test(a)) {
    try {
      return compareAsc(parseISO(a as string), parseISO(b as string));
    } catch {
      // 日付変換失敗時は単純文字比較
      return (a as string).localeCompare(b as string);
    }
  }

  // 文字列
  if (typeof a === "string" && typeof b === "string") {
    return a.localeCompare(b);
  }

  // 数値
  if (typeof a === "number" && typeof b === "number") {
    return a - b;
  }

  return 0;
};

export const articleFilter = (
  articles: ListingArticle[],
  criteria: string,
): ListingArticle[] => {
  const q = criteria.toLowerCase();
  return articles.filter((t) => {
    return t.title.toLowerCase().includes(q);
  });
};

export const PostTypeText = {
  "addon-post": "アドオン投稿",
  "addon-introduction": "アドオン紹介",
  page: "記事",
  markdown: "記事（マークダウン）",
} satisfies Record<ListingArticle["post_type"], string>;

export const StatusText = {
  publish: "公開中",
  reservation: "予約中",
  draft: "下書き",
  trash: "ゴミ箱",
  private: "非公開",
} satisfies Record<ListingArticle["status"], string>;

export const StatusClass = {
  publish: "bg-white hover:bg-gray-100",
  reservation: "bg-green-100 hover:bg-green-200",
  draft: "bg-orange-100 hover:bg-orange-200",
  trash: "bg-gray-200 hover:bg-gray-300",
  private: "bg-gray-200 hover:bg-gray-300",
} satisfies Record<ListingArticle["status"], string>;

export const deepCopy = <T>(obj: T): T => {
  return JSON.parse(JSON.stringify(obj));
};
