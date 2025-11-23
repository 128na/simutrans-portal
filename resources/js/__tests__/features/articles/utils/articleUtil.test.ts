import {
  articleFilter,
  compareArticleValues,
  createContents,
  deepCopy,
  PostTypeText,
  StatusClass,
  StatusText,
} from "@/features/articles/utils/articleUtil";
import { describe, expect, it } from "vitest";

describe("articleUtil", () => {
  describe("compareArticleValues", () => {
    it("両方nullの場合は0を返す", () => {
      expect(compareArticleValues(null, null)).toBe(0);
    });

    it("aがnullの場合は1を返す（末尾扱い）", () => {
      expect(compareArticleValues(null, "value")).toBe(1);
    });

    it("bがnullの場合は-1を返す", () => {
      expect(compareArticleValues("value", null)).toBe(-1);
    });

    it("数値の比較ができる", () => {
      expect(compareArticleValues(1, 2)).toBeLessThan(0);
      expect(compareArticleValues(2, 1)).toBeGreaterThan(0);
      expect(compareArticleValues(5, 5)).toBe(0);
    });

    it("文字列の比較ができる", () => {
      expect(compareArticleValues("abc", "def")).toBeLessThan(0);
      expect(compareArticleValues("def", "abc")).toBeGreaterThan(0);
      expect(compareArticleValues("test", "test")).toBe(0);
    });

    it("日付文字列の比較ができる", () => {
      expect(
        compareArticleValues("2024-01-01 00:00:00", "2024-12-31 00:00:00")
      ).toBeLessThan(0);
      expect(
        compareArticleValues("2024-12-31 00:00:00", "2024-01-01 00:00:00")
      ).toBeGreaterThan(0);
    });

    it("日付パースに失敗した場合は文字列比較にフォールバック", () => {
      const result = compareArticleValues("invalid-date", "2024-01-01");
      expect(typeof result).toBe("number");
    });

    it("Countオブジェクトの比較ができる", () => {
      const count1: Count = {
        id: 1,
        article_id: 1,
        user_id: 1,
        type: 1,
        period: "2024-01",
        count: 10,
      };
      const count2: Count = {
        id: 2,
        article_id: 1,
        user_id: 1,
        type: 1,
        period: "2024-01",
        count: 20,
      };
      expect(compareArticleValues(count1, count2)).toBeLessThan(0);
      expect(compareArticleValues(count2, count1)).toBeGreaterThan(0);
    });

    it("型が異なる場合は0を返す", () => {
      expect(compareArticleValues(123, "123")).toBe(0);
    });
  });

  describe("articleFilter", () => {
    const mockArticles: Article.MypageShow[] = [
      {
        id: 1,
        user_id: 1,
        slug: "test-1",
        title: "テスト記事1",
        status: "publish",
        post_type: "page",
        published_at: "2024-01-01 00:00:00",
        modified_at: "2024-01-01 00:00:00",
        total_view_count: null,
        total_conversion_count: null,
        attachments: [],
      },
      {
        id: 2,
        user_id: 1,
        slug: "test-2",
        title: "別のタイトル",
        status: "draft",
        post_type: "addon-post",
        published_at: null,
        modified_at: "2024-01-02 00:00:00",
        total_view_count: null,
        total_conversion_count: null,
        attachments: [],
      },
    ];

    it("検索条件なしの場合は全記事を返す", () => {
      const result = articleFilter(mockArticles, "");
      expect(result).toHaveLength(2);
    });

    it("空白のみの検索条件の場合は全記事を返す", () => {
      const result = articleFilter(mockArticles, "   ");
      expect(result).toHaveLength(2);
    });

    it("タイトルに含まれる記事をフィルタする", () => {
      const result = articleFilter(mockArticles, "テスト");
      expect(result).toHaveLength(1);
      expect(result[0].title).toBe("テスト記事1");
    });

    it("カタカナで検索できる", () => {
      const result = articleFilter(mockArticles, "テスト");
      expect(result).toHaveLength(1);
    });

    it("部分一致で検索する", () => {
      const result = articleFilter(mockArticles, "タイトル");
      expect(result).toHaveLength(1);
      expect(result[0].title).toBe("別のタイトル");
    });

    it("該当する記事がない場合は空配列を返す", () => {
      const result = articleFilter(mockArticles, "存在しない記事");
      expect(result).toHaveLength(0);
    });
  });

  describe("PostTypeText", () => {
    it("すべての投稿タイプに対応するテキストが定義されている", () => {
      expect(PostTypeText["addon-post"]).toBe("アドオン投稿");
      expect(PostTypeText["addon-introduction"]).toBe("アドオン紹介");
      expect(PostTypeText.page).toBe("記事");
      expect(PostTypeText.markdown).toBe("記事（マークダウン）");
    });
  });

  describe("StatusText", () => {
    it("すべてのステータスに対応するテキストが定義されている", () => {
      expect(StatusText.publish).toBe("公開中");
      expect(StatusText.reservation).toBe("予約中");
      expect(StatusText.draft).toBe("下書き");
      expect(StatusText.trash).toBe("ゴミ箱");
      expect(StatusText.private).toBe("非公開");
    });
  });

  describe("StatusClass", () => {
    it("すべてのステータスに対応するクラスが定義されている", () => {
      expect(StatusClass.publish).toContain("bg-white");
      expect(StatusClass.reservation).toContain("bg-green-100");
      expect(StatusClass.draft).toContain("bg-orange-100");
      expect(StatusClass.trash).toContain("bg-gray-200");
      expect(StatusClass.private).toContain("bg-gray-200");
    });
  });

  describe("deepCopy", () => {
    it("オブジェクトのディープコピーができる", () => {
      const original = { a: 1, b: { c: 2 } };
      const copy = deepCopy(original);
      expect(copy).toEqual(original);
      expect(copy).not.toBe(original);
      expect(copy.b).not.toBe(original.b);
    });

    it("配列のディープコピーができる", () => {
      const original = [1, 2, [3, 4]];
      const copy = deepCopy(original);
      expect(copy).toEqual(original);
      expect(copy).not.toBe(original);
    });

    it("プリミティブ値をコピーできる", () => {
      expect(deepCopy(123)).toBe(123);
      expect(deepCopy("string")).toBe("string");
      expect(deepCopy(true)).toBe(true);
    });

    it("nullをコピーできる", () => {
      expect(deepCopy(null)).toBe(null);
    });
  });

  describe("createContents", () => {
    it("page タイプのコンテンツを作成できる", () => {
      const content = createContents("page") as ArticleContent.Page & {
        type: string;
      };
      expect(content.type).toBe("page");
      expect(content.thumbnail).toBeNull();
      expect(content.sections).toEqual([]);
    });

    it("addon-post タイプのコンテンツを作成できる", () => {
      const content = createContents("addon-post") as ArticleContent.AddonPost & {
        type: string;
      };
      expect(content.type).toBe("addon-post");
      expect(content.thumbnail).toBeNull();
      expect(content.description).toBeNull();
      expect(content.file).toBeNull();
    });

    it("addon-introduction タイプのコンテンツを作成できる", () => {
      const content = createContents(
        "addon-introduction"
      ) as ArticleContent.AddonIntroduction & { type: string };
      expect(content.type).toBe("addon-introduction");
      expect(content.thumbnail).toBeNull();
      expect(content.link).toBeNull();
    });

    it("markdown タイプのコンテンツを作成できる", () => {
      const content = createContents("markdown") as ArticleContent.Markdown & {
        type: string;
      };
      expect(content.type).toBe("markdown");
      expect(content.thumbnail).toBeNull();
      expect(content.markdown).toBeNull();
    });
  });
});
