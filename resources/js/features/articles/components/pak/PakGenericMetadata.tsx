import React from "react";
import {
  PakInfoTable,
  type TableRow,
} from "@/features/articles/components/pak/PakInfoTable";
import type { PakMetadata } from "@/types/models";
import {
  getBuildingTypeName,
  getSystemTypeName,
  getEnablesString,
  getPlacementName,
  getClimateNames,
} from "./pakBuildingTranslations";
import {
  getWaytypeName,
  getEngineTypeName,
  getFreightTypeName,
} from "./pakTranslations";

interface Props {
  metadata: PakMetadata;
}

/**
 * 汎用的なPakオブジェクトメタデータ表示コンポーネント
 * vehicle含むすべてのオブジェクトタイプに対応
 */
const PakGenericMetadata: React.FC<Props> = ({ metadata }) => {
  const typeData = getTypeSpecificData(metadata);

  // すべてのタイプで1つのテーブル表示
  const rows: TableRow[] = [
    { label: "オブジェクト名", value: metadata.name },
    { label: "著作権", value: metadata.copyright || "" },
    {
      label: "オブジェクトタイプ",
      value: getObjectTypeLabel(metadata.objectType),
    },
  ];

  // タイプ固有データ
  if (typeData && Object.keys(typeData).length) {
    rows.splice(
      rows.length,
      0,
      ...buildDetailRows(metadata.objectType, typeData)
    );
  }

  return <PakInfoTable rows={rows} />;
};

/**
 * オブジェクトタイプのラベルを取得
 */
function getObjectTypeLabel(objectType: string): string {
  const labels: Record<string, string> = {
    vehicle: "車両",
    way: "軌道",
    wayobj: "軌道オブジェクト",
    bridge: "橋",
    tunnel: "トンネル",
    sign: "信号・標識",
    crossing: "踏切",
    citycar: "市内車両",
    factory: "産業施設",
    good: "輸送品目",
    building: "建物",
    pedestrian: "歩行者",
    tree: "樹木",
    groundobj: "地表オブジェクト",
    ground: "地形",
    sound: "サウンド",
    skin: "スキン",
  };

  return labels[objectType] || objectType;
}

/**
 * メタデータからタイプ固有データを抽出
 */
function getTypeSpecificData(
  metadata: PakMetadata
): Record<string, unknown> | null {
  // wayData, buildingData, factoryData等のキーを探す
  const dataKeys = (Object.keys(metadata) as Array<keyof PakMetadata>).filter(
    (key) => typeof key === "string" && key.endsWith("Data")
  );

  if (dataKeys.length === 0) {
    return null;
  }

  const dataKey = dataKeys[0];
  const data = metadata[dataKey];

  if (typeof data !== "object" || data === null) {
    return null;
  }

  return data as Record<string, unknown>;
}

/**
 * タイプ固有の詳細情報行を構築
 */
function buildDetailRows(
  objectType: string,
  typeData: Record<string, unknown>
): TableRow[] {
  switch (objectType) {
    case "vehicle":
      return buildVehicleRows(typeData);
    case "way":
      return buildWayRows(typeData);
    case "wayobj":
      return buildWayobjRows(typeData);
    case "bridge":
      return buildBridgeRows(typeData);
    case "tunnel":
      return buildTunnelRows(typeData);
    case "roadsign":
      return buildRoadsignRows(typeData);
    case "crossing":
      return buildCrossingRows(typeData);
    case "building":
      return buildBuildingRows(typeData);
    case "factory":
      return buildFactoryRows(typeData);
    case "good":
      return buildGoodRows(typeData);
    case "citycar":
      return buildCitycarRows(typeData);
    case "pedestrian":
      return buildPedestrianRows(typeData);
    case "tree":
      return buildTreeRows(typeData);
    case "groundobj":
      return buildGroundobjRows(typeData);
    case "ground":
      return buildGroundRows(typeData);
    case "sound":
      return buildSoundRows(typeData);
    case "skin":
      return buildSkinRows(typeData);
    default:
      return buildGenericRows(typeData);
  }
}

/**
 * Vehicle（車両）の詳細行
 *
 * Unit conversions (based on Simutrans source code):
 * - intro_date/retire_date: stored as (year*12 + month-1) → display as "YYYY年M月"
 * - weight: stored in kg → display in tons (÷1000)
 * - price: stored in 1/100 Cr → display in Cr (×100)
 * - running_cost: stored in 1/100 Cr/km → display in Cr/km (÷100)
 * - maintenance: stored in 1/100 Cr/month → display in Cr/month (÷100)
 * - len: stored in 1/8 tile units → display as-is (raw value)
 * - gear: stored as gear*64 (64=1.00) → display as-is (raw value)
 *
 * References:
 * - simutrans/descriptor/reader/vehicle_reader.cc (version 10+: weight in kg)
 * - simutrans/descriptor/vehicle_desc.h (get_weight, get_running_cost, etc.)
 */
function buildVehicleRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "導入年月",
      value:
        data.intro_date !== undefined
          ? `${Math.floor(Number(data.intro_date) / 12)}年${(Number(data.intro_date) % 12) + 1}月`
          : undefined,
    },
    {
      label: "引退年月",
      value:
        data.retire_date !== undefined
          ? `${Math.floor(Number(data.retire_date) / 12)}年${(Number(data.retire_date) % 12) + 1}月`
          : undefined,
    },
    {
      label: "最高速度",
      value: data.topspeed !== undefined ? `${data.topspeed} km/h` : undefined,
    },
    {
      label: "出力",
      value: data.power !== undefined ? `${data.power} kW` : undefined,
    },
    {
      label: "ギア",
      value: data.gear !== undefined ? String(data.gear) : undefined,
    },
    {
      label: "重量",
      value:
        data.weight !== undefined
          ? `${(Number(data.weight) / 1000).toFixed(1)} t`
          : undefined,
    },
    {
      label: "軸重",
      value: data.axle_load !== undefined ? `${data.axle_load} t` : undefined,
    },
    {
      label: "定員/容量",
      value: data.capacity !== undefined ? String(data.capacity) : undefined,
    },
    {
      label: "輸送品目",
      value: getFreightTypeName(data.freight_type as string | undefined),
    },
    {
      label: "購入価格",
      value:
        data.price !== undefined
          ? `${(Number(data.price) * 100).toLocaleString()} Cr`
          : undefined,
    },
    {
      label: "運行費用",
      value:
        data.running_cost !== undefined
          ? `${(Number(data.running_cost) / 100).toLocaleString()} Cr/km`
          : undefined,
    },
    {
      label: "維持費",
      value:
        data.maintenance !== undefined
          ? `${(Number(data.maintenance) / 100).toLocaleString()} Cr/月`
          : undefined,
    },
    {
      label: "軌道タイプ",
      value: getWaytypeName(data.wtyp as number | undefined),
    },
    {
      label: "エンジンタイプ",
      value: getEngineTypeName(
        (data.engine_type_str as string | undefined) ||
          (data.engine_type as number | undefined)
      ),
    },
    {
      label: "長さ",
      value: data.len !== undefined ? String(data.len) : undefined,
    },
  ];
}

/**
 * Way（軌道）の詳細行
 */
function buildWayRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: data.wtyp_str
        ? getWaytypeName(Number(data.wtyp))
        : String(data.wtyp || ""),
    },
    {
      label: "システムタイプ",
      value: data.styp_str
        ? getSystemTypeName(String(data.styp_str))
        : String(data.styp || ""),
    },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "最大積載量",
      value: data.max_weight ? `${data.max_weight} t` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Bridge（橋）の詳細行
 */
function buildBridgeRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: data.wtyp_str
        ? getWaytypeName(Number(data.wtyp))
        : String(data.wtyp || ""),
    },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "最大積載量",
      value: data.max_weight ? `${data.max_weight} t` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    { label: "最大長", value: data.max_length ? `${data.max_length}` : "" },
    {
      label: "最小長",
      value: data.min_length ? `${data.min_length}` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Tunnel（トンネル）の詳細行
 */
function buildTunnelRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: data.wtyp_str
        ? getWaytypeName(Number(data.wtyp))
        : String(data.wtyp || ""),
    },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "最大積載量",
      value: data.max_weight ? `${data.max_weight} t` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Building（建物）の詳細行
 */
function buildBuildingRows(data: Record<string, unknown>): TableRow[] {
  const rows: TableRow[] = [
    {
      label: "建物タイプ",
      value: data.type_str
        ? getBuildingTypeName(String(data.type_str))
        : String(data.type || ""),
    },
    { label: "レベル", value: String(data.level || "") },
    { label: "サイズ", value: `${data.size_x || 1} x ${data.size_y || 1}` },
    { label: "レイアウト数", value: String(data.layouts || "") },
  ];

  // 軌道タイプ（停留所など）
  if (data.waytype_str) {
    rows.push({
      label: "軌道タイプ",
      value: getWaytypeName(Number(data.extra_data || 0)),
    });
  }

  // 取扱い品目（停留所など）
  if (data.enables_str) {
    rows.push({
      label: "取扱い品目",
      value: getEnablesString(String(data.enables_str)),
    });
  }

  rows.push(
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    }
  );

  return rows;
}

/**
 * Factory（産業施設）の詳細行
 */
function buildFactoryRows(data: Record<string, unknown>): TableRow[] {
  const rows: TableRow[] = [
    { label: "生産力", value: String(data.productivity || "") },
    { label: "生産範囲", value: String(data.range || "") },
    { label: "配置確率", value: String(data.distribution_weight || "") },
  ];

  // 配置場所
  if (data.placement_str) {
    rows.push({
      label: "配置場所",
      value: getPlacementName(String(data.placement_str)),
    });
  }

  rows.push(
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    }
  );

  return rows;
}

/**
 * Good（輸送品目）の詳細行
 */
function buildGoodRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "カテゴリ", value: String(data.catg_index || "") },
    { label: "重量", value: data.weight ? `${data.weight}` : "" },
    { label: "価値", value: String(data.value || "") },
    { label: "速度ボーナス", value: String(data.speed_bonus || "") },
  ];
}

/**
 * Tree（樹木）の詳細行
 */
function buildTreeRows(data: Record<string, unknown>): TableRow[] {
  const rows: TableRow[] = [
    { label: "種類数", value: String(data.number_of_seasons || "") },
  ];

  // 許可気候
  if (data.allowed_climates_str) {
    rows.push({
      label: "許可気候",
      value: getClimateNames(String(data.allowed_climates_str)),
    });
  }

  return rows;
}

/**
 * Groundobj（地形オブジェクト）の詳細行
 */
function buildGroundobjRows(data: Record<string, unknown>): TableRow[] {
  const rows: TableRow[] = [];

  // 許可気候
  if (data.allowed_climates_str) {
    rows.push({
      label: "許可気候",
      value: getClimateNames(String(data.allowed_climates_str)),
    });
  }

  // 軌道タイプ
  if (data.waytype_str) {
    rows.push({
      label: "軌道タイプ",
      value: getWaytypeName(Number(data.waytype || 0)),
    });
  }

  return rows;
}

/**
 * Wayobj（軌道オブジェクト）の詳細行
 */
function buildWayobjRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: data.wtyp_str
        ? getWaytypeName(Number(data.wtyp))
        : String(data.wtyp || ""),
    },
    {
      label: "所有タイプ",
      value: data.own_wtyp_str
        ? String(data.own_wtyp_str)
        : String(data.own_wtyp || ""),
    },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Roadsign（信号・標識）の詳細行
 */
function buildRoadsignRows(data: Record<string, unknown>): TableRow[] {
  const rows: TableRow[] = [];

  // 最低速度
  if (data.min_speed !== undefined && data.min_speed !== null) {
    rows.push({
      label: "最低速度",
      value: `${data.min_speed} km/h`,
    });
  }

  rows.push(
    {
      label: "軌道タイプ",
      value: data.wtyp_str
        ? getWaytypeName(Number(data.wtyp))
        : String(data.wtyp || ""),
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    }
  );

  return rows;
}

/**
 * Crossing（踏切）の詳細行
 */
function buildCrossingRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "軌道タイプ1",
      value: data.ns_wtyp_str
        ? getWaytypeName(Number(data.ns_wtyp))
        : String(data.ns_wtyp || ""),
    },
    {
      label: "軌道タイプ2",
      value: data.ew_wtyp_str
        ? getWaytypeName(Number(data.ew_wtyp))
        : String(data.ew_wtyp || ""),
    },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Citycar（市内車両）の詳細行
 */
function buildCitycarRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "配置確率",
      value: String(data.distribution_weight || ""),
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Pedestrian（歩行者）の詳細行
 */
function buildPedestrianRows(data: Record<string, unknown>): TableRow[] {
  return [
    {
      label: "配置確率",
      value: String(data.distribution_weight || ""),
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Ground（地形）の詳細行
 */
function buildGroundRows(data: Record<string, unknown>): TableRow[] {
  // groundオブジェクトはデータフィールドを持たない（画像のみ）
  return [
    {
      label: "データ",
      value: data.has_data ? "あり" : "なし（画像のみ）",
    },
  ];
}

/**
 * Sound（サウンド）の詳細行
 */
function buildSoundRows(data: Record<string, unknown>): TableRow[] {
  // soundオブジェクトはデータフィールドを持たない
  return [
    {
      label: "データ",
      value: data.has_data ? "あり" : "なし（音声ファイルのみ）",
    },
  ];
}

/**
 * Skin（スキン）の詳細行
 */
function buildSkinRows(data: Record<string, unknown>): TableRow[] {
  // skinオブジェクトはデータフィールドを持たない（画像のみ）
  const rows: TableRow[] = [
    {
      label: "データ",
      value: data.has_data ? "あり" : "なし（画像のみ）",
    },
  ];

  // サブタイプ（smoke等）
  if (data.object_subtype) {
    rows.push({
      label: "サブタイプ",
      value: String(data.object_subtype),
    });
  }

  return rows;
}

/**
 * 汎用的な詳細行（不明なタイプの場合）
 */
function buildGenericRows(data: Record<string, unknown>): TableRow[] {
  return Object.entries(data)
    .filter(([key]) => !key.endsWith("_str")) // *_str系は除外（元データと重複）
    .map(([key, value]) => ({
      label: key,
      value: String(value ?? ""),
    }));
}

/**
 * 日付フォーマット (year*12+month → YYYY/MM)
 */
function formatDate(monthsSinceZero: number): string {
  if (monthsSinceZero === 0 || monthsSinceZero >= 999912) {
    return "";
  }

  const year = Math.floor(monthsSinceZero / 12);
  const month = (monthsSinceZero % 12) + 1;

  return `${year}/${month.toString().padStart(2, "0")}`;
}

export default PakGenericMetadata;
