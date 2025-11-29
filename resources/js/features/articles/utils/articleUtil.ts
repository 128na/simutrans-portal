import { compareAsc, format, parseISO } from "date-fns";
export const compareArticleValues = (a: unknown, b: unknown): number => {
  // null を末尾扱いにする
  if (a == null && b == null) return 0;

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
  articles: Article.MypageShow[],
  criteria: string
) => {
  const q = criteria.trim().toLowerCase();
  if (!q) return articles;

  return articles.filter((t) => t.title.toLowerCase().includes(q));
};

export const PostTypeText: Record<ArticlePostType, string> = {
  "addon-post": "アドオン投稿",
  "addon-introduction": "アドオン紹介",
  page: "記事",
  markdown: "記事（マークダウン）",
};

export const StatusText: Record<ArticleStatus, string> = {
  publish: "公開中",
  reservation: "予約中",
  draft: "下書き",
  trash: "ゴミ箱",
  private: "非公開",
};

export const StatusClass: Record<ArticleStatus, string> = {
  publish: "bg-white hover:bg-gray-100",
  reservation: "bg-green-100 hover:bg-green-200",
  draft: "bg-orange-100 hover:bg-orange-200",
  trash: "bg-gray-200 hover:bg-gray-300",
  private: "bg-gray-200 hover:bg-gray-300",
};

export const deepCopy = <T>(obj: T): T => {
  return JSON.parse(JSON.stringify(obj));
};

export const createContents = (postType: ArticlePostType) => {
  switch (postType) {
    case "page":
      return {
        type: "page",
        thumbnail: null,
        sections: [],
      } as ArticleContent.Page;
    case "addon-post":
      return {
        type: "addon-post",
        thumbnail: null,
        description: null,
        file: null,
        author: null,
        license: null,
        thanks: null,
      } as ArticleContent.AddonPost;
    case "addon-introduction":
      return {
        type: "addon-introduction",
        thumbnail: null,
        description: null,
        link: null,
        author: null,
        license: null,
        thanks: null,
        agreement: false,
        exclude_link_check: false,
      } as ArticleContent.AddonIntroduction;
    case "markdown":
      return {
        type: "markdown",
        thumbnail: null,
        markdown: null,
      } as ArticleContent.Markdown;
    default:
      throw new Error("invalid post type");
  }
};
export const createArticle = (
  postType: ArticlePostType,
  user: User.MypageShow
) => {
  return {
    id: null,
    user_id: user.id,
    title: "",
    slug: "",
    post_type: postType,
    status: "draft",
    contents: createContents(postType),
    categories: [],
    tags: [],
    articles: [],
    attachments: [],
    published_at: null,
    modified_at: null,
    created_at: null,
    updated_at: null,
  } as Article.MypageEdit;
};

export const typedKeys = <T extends object>(obj: T): (keyof T)[] => {
  return Object.keys(obj) as (keyof T)[];
};

export const formatArticleDate = (dateStr: string | null) => {
  if (!dateStr) return "-";
  const date = new Date(dateStr);
  return format(date, "yyyy/MM/dd HH:mm");
};
