import { describe, expect, it } from "vitest";
import { buildDetailRows } from "@/features/articles/components/pak/pakRowBuilders";
import type {
  BridgeData,
  GroundobjData,
  SoundData,
  TreeData,
  TunnelData,
  VehicleData,
  WayData,
} from "@/types/models";

function labelOf(rows: ReturnType<typeof buildDetailRows>, label: string) {
  return rows.find((r) => r.label === label)?.value;
}

describe("buildDetailRows", () => {
  describe("vehicle", () => {
    const base: VehicleData = {
      waytype: 2,
      engine_type: 2,
      capacity: 200,
      topspeed: 130,
      price: 500,
      running_cost: 120,
      maintenance: 80,
      power: 300,
      gear: 64,
      weight: 50000,
      axle_load: 15,
      len: 8,
      loading_time: 2000,
      leader_count: 1,
      trailer_count: 3,
      intro_date: 1990 * 12,
      retire_date: 2020 * 12,
    };

    it("軸重を表示する", () => {
      const rows = buildDetailRows("vehicle", base as Record<string, unknown>);
      expect(labelOf(rows, "軸重")).toBe("15 t");
    });

    it("9999の軸重は無制限を表示する", () => {
      const rows = buildDetailRows("vehicle", { ...base, axle_load: 9999 } as Record<string, unknown>);
      expect(labelOf(rows, "軸重")).toBe("無制限");
    });

    it("乗降時間を表示する", () => {
      const rows = buildDetailRows("vehicle", base as Record<string, unknown>);
      expect(labelOf(rows, "乗降時間")).toBe("2000 ms");
    });

    it("維持費を表示する", () => {
      const rows = buildDetailRows("vehicle", base as Record<string, unknown>);
      expect(labelOf(rows, "維持費")).toBe("0.80 Cr/月");
    });

    it("先頭/付随連結可能数を表示する", () => {
      const rows = buildDetailRows("vehicle", base as Record<string, unknown>);
      expect(labelOf(rows, "先頭連結可能数")).toBe(1);
      expect(labelOf(rows, "付随連結可能数")).toBe(3);
    });
  });

  describe("way", () => {
    const base: WayData = {
      waytype: 2,
      topspeed: 130,
      max_weight: 500,
      axle_load: 20,
      price: 1000,
      maintenance: 200,
      number_of_seasons: 1,
      clip_below: true,
      intro_date: 1990 * 12,
      retire_date: 2020 * 12,
    };

    it("最大重量を表示する", () => {
      const rows = buildDetailRows("way", base as Record<string, unknown>);
      expect(labelOf(rows, "最大重量")).toBe("500 t");
    });

    it("軸重制限を表示する", () => {
      const rows = buildDetailRows("way", base as Record<string, unknown>);
      expect(labelOf(rows, "軸重制限")).toBe("20 t");
    });

    it("下部クリップを表示する", () => {
      const rows = buildDetailRows("way", base as Record<string, unknown>);
      expect(labelOf(rows, "下部クリップ")).toBe("Yes");
    });

    it("clip_below=false はNoを表示する", () => {
      const rows = buildDetailRows("way", { ...base, clip_below: false } as Record<string, unknown>);
      expect(labelOf(rows, "下部クリップ")).toBe("No");
    });
  });

  describe("bridge", () => {
    const base: BridgeData = {
      waytype: 2,
      topspeed: 130,
      axle_load: 25,
      price: 2000,
      maintenance: 300,
      max_length: 10,
      max_height: 5,
      pillars_every: 4,
      number_of_seasons: 1,
      clip_below: false,
      intro_date: 1990 * 12,
      retire_date: 2020 * 12,
    };

    it("軸重制限を表示する", () => {
      const rows = buildDetailRows("bridge", base as Record<string, unknown>);
      expect(labelOf(rows, "軸重制限")).toBe("25 t");
    });

    it("最大高さを表示する", () => {
      const rows = buildDetailRows("bridge", base as Record<string, unknown>);
      expect(labelOf(rows, "最大高さ")).toBe(5);
    });

    it("支柱間隔をタイル単位で表示する", () => {
      const rows = buildDetailRows("bridge", base as Record<string, unknown>);
      expect(labelOf(rows, "支柱間隔")).toBe("4 タイル");
    });

    it("支柱間隔=0は支柱なしを表示する", () => {
      const rows = buildDetailRows("bridge", { ...base, pillars_every: 0 } as Record<string, unknown>);
      expect(labelOf(rows, "支柱間隔")).toBe("支柱なし");
    });

    it("下部クリップを表示する", () => {
      const rows = buildDetailRows("bridge", base as Record<string, unknown>);
      expect(labelOf(rows, "下部クリップ")).toBe("No");
    });
  });

  describe("tunnel", () => {
    const base: TunnelData = {
      waytype: 2,
      topspeed: 130,
      axle_load: 30,
      price: 3000,
      maintenance: 400,
      number_of_seasons: 0,
      has_way: true,
      broad_portals: false,
      intro_date: 1990 * 12,
      retire_date: 2020 * 12,
    };

    it("軸重制限を表示する", () => {
      const rows = buildDetailRows("tunnel", base as Record<string, unknown>);
      expect(labelOf(rows, "軸重制限")).toBe("30 t");
    });

    it("内部線路を表示する", () => {
      const rows = buildDetailRows("tunnel", base as Record<string, unknown>);
      expect(labelOf(rows, "内部線路")).toBe("Yes");
    });

    it("広口ポータルを表示する", () => {
      const rows = buildDetailRows("tunnel", base as Record<string, unknown>);
      expect(labelOf(rows, "広口ポータル")).toBe("No");
    });
  });

  describe("tree", () => {
    const base: TreeData = {
      allowed_climates: 0x70,
      climate_names: ["temperate_climate", "tundra_climate", "rocky_climate"],
      distribution_weight: 5,
      number_of_seasons: 1,
    };

    it("出現確率を表示する", () => {
      const rows = buildDetailRows("tree", base as Record<string, unknown>);
      expect(labelOf(rows, "出現確率（重み）")).toBe(5);
    });

    it("対応気候を日本語で表示する", () => {
      const rows = buildDetailRows("tree", base as Record<string, unknown>);
      expect(labelOf(rows, "対応気候")).toBe("温帯, ツンドラ, 岩地");
    });

    it("降雪対応を表示する", () => {
      const rows = buildDetailRows("tree", base as Record<string, unknown>);
      expect(labelOf(rows, "降雪対応")).toBe("Yes");
    });
  });

  describe("groundobj", () => {
    const base: GroundobjData = {
      allowed_climates: 0x10,
      climate_names: ["temperate_climate"],
      distribution_weight: 3,
      number_of_seasons: 0,
      trees_on_top: true,
      speed: 0,
      waytype: 0,
      price: 100,
    };

    it("出現確率を表示する", () => {
      const rows = buildDetailRows("groundobj", base as Record<string, unknown>);
      expect(labelOf(rows, "出現確率（重み）")).toBe(3);
    });

    it("対応気候を日本語で表示する", () => {
      const rows = buildDetailRows("groundobj", base as Record<string, unknown>);
      expect(labelOf(rows, "対応気候")).toBe("温帯");
    });

    it("speed=0は静止を表示する", () => {
      const rows = buildDetailRows("groundobj", base as Record<string, unknown>);
      expect(labelOf(rows, "移動速度")).toBe("静止");
    });

    it("上に木を生やせるをYesで表示する", () => {
      const rows = buildDetailRows("groundobj", base as Record<string, unknown>);
      expect(labelOf(rows, "上に木を生やせる")).toBe("Yes");
    });
  });

  describe("sound", () => {
    const base: SoundData = {
      version: 2,
      sound_id: 42,
      filename: "horn.ogg",
    };

    it("サウンドIDを表示する", () => {
      const rows = buildDetailRows("sound", base as Record<string, unknown>);
      expect(labelOf(rows, "サウンドID")).toBe(42);
    });

    it("ファイル名を表示する", () => {
      const rows = buildDetailRows("sound", base as Record<string, unknown>);
      expect(labelOf(rows, "ファイル名")).toBe("horn.ogg");
    });

    it("filenameなしは空文字を表示する", () => {
      const rows = buildDetailRows("sound", { ...base, filename: undefined } as Record<string, unknown>);
      expect(labelOf(rows, "ファイル名")).toBe("");
    });
  });

  describe("skin系 (menu/cursor/symbol/field/smoke/miscimages/ground)", () => {
    it.each(["menu", "cursor", "symbol", "field", "smoke", "miscimages", "ground"])(
      "%s は空の行配列を返す",
      (type) => {
        const rows = buildDetailRows(type, { has_data: false, object_subtype: type });
        expect(rows).toHaveLength(0);
      }
    );
  });
});
