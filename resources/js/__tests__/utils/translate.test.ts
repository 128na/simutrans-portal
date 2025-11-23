import { t } from "@/utils/translate";
import { describe, expect, it } from "vitest";

// Note: ja.json is gitignored and generated at build time
// These tests work with the fallback behavior when translations are missing

describe("translate ユーティリティ", () => {
  describe("t 関数", () => {
    it("変数なしで文字列を返す", () => {
      const result = t("some.key");
      expect(result).toBeDefined();
      expect(typeof result).toBe("string");
    });

    it("変数を置換する", () => {
      // キーが存在しない場合はキーがそのまま返され、変数が置換される
      const result = t("Hello :name", { name: "World" });
      expect(result).toContain("World");
      expect(result).not.toContain(":name");
    });

    it("複数の変数を置換する", () => {
      const result = t(":greeting :name, you are :age years old", {
        greeting: "Hello",
        name: "Alice",
        age: "30",
      });
      expect(result).toBe("Hello Alice, you are 30 years old");
    });

    it("変数が提供されない場合は元のテンプレートを返す", () => {
      const result = t("Hello :name");
      expect(result).toContain(":name");
    });

    it("空の変数オブジェクトでも動作する", () => {
      const result = t("plain.text", {});
      expect(result).toBe("plain.text");
    });

    it("存在しない変数はそのまま残る", () => {
      const result = t(":greeting :name", { greeting: "Hello" });
      expect(result).toContain("Hello");
      expect(result).toContain(":name");
    });

    it("同じ変数が複数回出現する場合すべて置換される", () => {
      const result = t(":name loves :name", { name: "John" });
      expect(result).toBe("John loves John");
    });

    it("変数置換が正しく機能する", () => {
      const result = t("Welcome :user", { user: "Admin" });
      expect(result).toBe("Welcome Admin");
    });

    it("デフォルトロケールで動作する", () => {
      const result = t("test.key");
      // キーが存在しない場合はキー自体が返される
      expect(result).toBe("test.key");
    });
  });
});
