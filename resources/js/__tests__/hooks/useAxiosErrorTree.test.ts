import { beforeEach, describe, expect, it } from "vitest";
import { useAxiosError } from "@/hooks/useAxiosError";

describe("useAxiosError のツリー検索", () => {
  beforeEach(() => {
    useAxiosError.getState().setError({
      errors: {
        "article.articles": ["多すぎます。"],
        "article.articles.0": ["存在しません。"],
        "article.articles_extra": ["別のキー。"],
        "article.tags.1": ["存在しません。"],
      },
      message: null,
    });
  });

  it("キー自身と配下のキーを見つける。前方一致だけの別キーは含めない", () => {
    const { hasErrorTree } = useAxiosError.getState();
    expect(hasErrorTree("article.articles")).toBe(true);
    expect(hasErrorTree("article.tags")).toBe(true);
    expect(hasErrorTree("article.contents")).toBe(false);
    expect(hasErrorTree("article.art")).toBe(false);
  });

  it("配下のメッセージを重複なしでまとめる", () => {
    const { getErrorTree } = useAxiosError.getState();
    expect(getErrorTree("article.articles")).toBe(
      "多すぎます。、存在しません。"
    );
    expect(getErrorTree("article.tags")).toBe("存在しません。");
    expect(getErrorTree("article.contents")).toBeNull();
  });
});
