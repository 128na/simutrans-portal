import { describe, expect, it } from "vitest";
import {
  formatAxleLoad,
  formatBoolean,
  formatBuildPrice,
  formatClimates,
  formatDate,
  formatMaintenanceCost,
  formatRunningCost,
  formatSpeed,
  formatWeight,
} from "@/features/articles/components/pak/formatter";

describe("formatter", () => {
  describe("formatDate", () => {
    it("年月に変換できる", () => {
      expect(formatDate(1990 * 12)).toBe("1990/01");
      expect(formatDate(1990 * 12 + 5)).toBe("1990/06");
    });

    it("0 または undefined は空文字を返す", () => {
      expect(formatDate(0)).toBe("");
      expect(formatDate(undefined)).toBe("");
    });

    it("999912以上は空文字を返す（引退なし）", () => {
      expect(formatDate(999912)).toBe("");
      expect(formatDate(999999)).toBe("");
    });
  });

  describe("formatSpeed", () => {
    it("km/hを付けて返す", () => {
      expect(formatSpeed(120)).toBe("120 km/h");
    });

    it("undefinedは空文字を返す", () => {
      expect(formatSpeed(undefined)).toBe("");
    });
  });

  describe("formatWeight", () => {
    it("kgをトンに変換する", () => {
      expect(formatWeight(5000)).toBe("5.0 t");
    });

    it("undefinedは空文字を返す", () => {
      expect(formatWeight(undefined)).toBe("");
    });
  });

  describe("formatBuildPrice", () => {
    it("×100して Cr 単位で返す", () => {
      expect(formatBuildPrice(100)).toBe("10,000 Cr");
    });
  });

  describe("formatRunningCost", () => {
    it("÷100して Cr/km 単位で返す", () => {
      expect(formatRunningCost(200)).toBe("2.00 Cr/km");
    });
  });

  describe("formatMaintenanceCost", () => {
    it("÷100して Cr/月 単位で返す", () => {
      expect(formatMaintenanceCost(150)).toBe("1.50 Cr/月");
    });
  });

  describe("formatBoolean", () => {
    it("trueはYesを返す", () => {
      expect(formatBoolean(true)).toBe("Yes");
    });

    it("falseはNoを返す", () => {
      expect(formatBoolean(false)).toBe("No");
    });

    it("undefinedは空文字を返す", () => {
      expect(formatBoolean(undefined)).toBe("");
    });
  });

  describe("formatAxleLoad", () => {
    it("通常の値はトン単位で返す", () => {
      expect(formatAxleLoad(20)).toBe("20 t");
    });

    it("9999以上は無制限を返す", () => {
      expect(formatAxleLoad(9999)).toBe("無制限");
      expect(formatAxleLoad(10000)).toBe("無制限");
    });

    it("undefinedは空文字を返す", () => {
      expect(formatAxleLoad(undefined)).toBe("");
    });
  });

  describe("formatClimates", () => {
    it("気候名を日本語に変換して返す", () => {
      expect(formatClimates(["temperate_climate", "tundra_climate"])).toBe(
        "温帯, ツンドラ"
      );
    });

    it("全気候を変換できる", () => {
      const allClimates = [
        "water_climate",
        "desert_climate",
        "tropic_climate",
        "mediterran_climate",
        "temperate_climate",
        "tundra_climate",
        "rocky_climate",
        "arctic_climate",
      ];
      const result = formatClimates(allClimates);
      expect(result).toBe(
        "水域, 砂漠, 熱帯, 地中海性, 温帯, ツンドラ, 岩地, 極地"
      );
    });

    it("空配列は空文字を返す", () => {
      expect(formatClimates([])).toBe("");
    });

    it("undefinedは空文字を返す", () => {
      expect(formatClimates(undefined)).toBe("");
    });
  });
});
