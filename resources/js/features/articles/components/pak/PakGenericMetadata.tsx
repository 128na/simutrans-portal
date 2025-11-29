import React from "react";
import {
  PakInfoTable,
  type TableRow,
} from "@/features/articles/components/pak/PakInfoTable";
import type {
  BridgeData,
  BuildingData,
  CitycarData,
  CrossingData,
  FactoryData,
  GoodData,
  PakMetadata,
  PedestrianData,
  RoadsignData,
  TunnelData,
  VehicleData,
  WayData,
  wayobjData,
} from "@/types/models";
import {
  getBuildingTypeName,
  getSystemTypeName,
  getEnablesString,
  getPlacementName,
} from "./pakBuildingTranslations";
import {
  formatDate,
  formatGear,
  formatGoodCategory,
  formatMaintenanceCost,
  formatNum,
  formatPower,
  formatBuildPrice,
  formatRunningCost,
  formatSpeed,
  formatWeight,
  formatWaytype,
  formatEngineType,
  formatFreightType,
  formatGoodMetric,
  formatBoolean,
  formatSignalType,
  formatSignalAttribute,
  formatGoodPrice,
} from "./formatter";
import { ObjectType } from "./pakConstants";

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
      label: "タイプ",
      value: getObjectTypeLabel(metadata.objectType as ObjectType),
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
function getObjectTypeLabel(objectType: ObjectType): string {
  const labels: Record<ObjectType, string> = {
    vehicle: "車両",
    way: "軌道",
    wayobj: "軌道オブジェクト",
    bridge: "橋",
    tunnel: "トンネル",
    roadsign: "信号・標識",
    crossing: "踏切",
    citycar: "市内車両",
    factory: "産業施設",
    good: "輸送品目",
    building: "建物",
    pedestrian: "歩行者",
    tree: "樹木",
  };

  return labels[objectType]
    ? `${labels[objectType]} (${objectType})`
    : objectType;
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
      return buildVehicleRows(typeData as unknown as VehicleData);
    case "way":
      return buildWayRows(typeData as unknown as WayData);
    case "wayobj":
      return buildWayobjRows(typeData as unknown as wayobjData);
    case "bridge":
      return buildBridgeRows(typeData as unknown as BridgeData);
    case "tunnel":
      return buildTunnelRows(typeData as unknown as TunnelData);
    case "roadsign":
      return buildRoadsignRows(typeData as unknown as RoadsignData);
    case "crossing":
      return buildCrossingRows(typeData as unknown as CrossingData);
    case "building":
      return buildBuildingRows(typeData as unknown as BuildingData);
    case "factory":
      return buildFactoryRows(typeData as unknown as FactoryData);
    case "good":
      return buildGoodRows(typeData as unknown as GoodData);
    case "citycar":
      return buildCitycarRows(typeData as unknown as CitycarData);
    case "pedestrian":
      return buildPedestrianRows(typeData as unknown as PedestrianData);
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
function buildVehicleRows(data: VehicleData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype as number | undefined),
    },
    {
      label: "エンジンタイプ",
      value: formatEngineType(data.engine_type),
    },
    {
      label: "定員/容量",
      value: formatNum(data.capacity),
    },
    {
      label: "最高速度",
      value: formatSpeed(data.topspeed),
    },
    {
      label: "輸送品目",
      value: formatFreightType(data.freight_type),
    },
    {
      label: "購入価格",
      value: formatBuildPrice(data.price),
    },
    {
      label: "運行費用",
      value: formatRunningCost(data.running_cost, 2),
    },
    {
      label: "出力",
      value: formatPower(data.power),
    },
    {
      label: "ギア",
      value: formatGear(data.gear),
    },
    {
      label: "重量",
      value: formatWeight(data.weight),
    },
    {
      label: "長さ",
      value: data.len,
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Way（軌道）の詳細行
 */
function buildWayRows(data: WayData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype),
    },
    {
      label: "システムタイプ",
      value: getSystemTypeName(data.styp),
    },
    {
      label: "最高速度",
      value: formatSpeed(data.topspeed),
    },
    {
      label: "建設費",
      value: formatBuildPrice(data.price),
    },
    {
      label: "維持費",
      value: formatMaintenanceCost(data.maintenance),
    },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Bridge（橋）の詳細行
 */
function buildBridgeRows(data: BridgeData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype),
    },
    {
      label: "最高速度",
      value: formatSpeed(data.topspeed),
    },
    {
      label: "建設費",
      value: formatBuildPrice(data.price),
    },
    {
      label: "維持費",
      value: formatMaintenanceCost(data.maintenance),
    },
    { label: "最大長", value: data.max_length || "無制限" },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Tunnel（トンネル）の詳細行
 */
function buildTunnelRows(data: TunnelData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype),
    },
    {
      label: "最高速度",
      value: formatSpeed(data.topspeed),
    },
    {
      label: "建設費",
      value: formatBuildPrice(data.price),
    },
    {
      label: "維持費",
      value: formatMaintenanceCost(data.maintenance),
    },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Building（建物）の詳細行
 */
function buildBuildingRows(data: BuildingData): TableRow[] {
  return [
    {
      label: "建物タイプ",
      value: getBuildingTypeName(data.type ?? 0),
    },
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype),
    },
    {
      label: "取扱い品目",
      value: getEnablesString(data.enables ?? 0),
    },
    { label: "レベル", value: data.level || "" },
    { label: "サイズ", value: `${data.size_x || 1} x ${data.size_y || 1}` },
    { label: "レイアウト数", value: data.layouts || "" },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Factory（産業施設）の詳細行
 */
function buildFactoryRows(data: FactoryData): TableRow[] {
  return [
    { label: "生産力", value: data.productivity || "" },
    { label: "生産範囲", value: data.range || "" },
    { label: "出現確率（重み）", value: data.distribution_weight || "" },
    {
      label: "配置場所",
      value: getPlacementName(data.placement),
    },
    ...(data.input || []).flatMap((item, index) => [
      {
        label: `入力貨物名${index + 1}`,
        value: item.good,
      },
    ]),
    ...(data.output || []).flatMap((item, index) => [
      {
        label: `出力貨物名${index + 1}`,
        value: item.good,
      },
    ]),
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Good（輸送品目）の詳細行
 */
function buildGoodRows(data: GoodData): TableRow[] {
  return [
    { label: "カテゴリ", value: formatGoodCategory(data.catg) },
    {
      label: "重量",
      value: formatGoodMetric(data.weight_per_unit, data.metric),
    },
    { label: "基本価格", value: formatGoodPrice(data.base_value, data.metric) },
    { label: "速度ボーナス基準", value: data.speed_bonus },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Wayobj（軌道オブジェクト）の詳細行
 */
function buildWayobjRows(data: wayobjData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype),
    },
    {
      label: "電化/非電化",
      value: data.waytype !== data.own_waytype ? "電化" : "非電化",
    },
    {
      label: "最高速度",
      value: formatSpeed(data.topspeed),
    },
    {
      label: "建設費",
      value: formatBuildPrice(data.price),
    },
    {
      label: "維持費",
      value: formatMaintenanceCost(data.maintenance),
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Roadsign（信号・標識）の詳細行
 */
function buildRoadsignRows(data: RoadsignData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype),
    },
    {
      label: "最低速度",
      value: formatSpeed(data.min_speed),
    },
    {
      label: "信号/標識",
      value: formatSignalType(data),
    },
    {
      label: "属性",
      value: formatSignalAttribute(data),
    },
    {
      label: "建設費",
      value: formatBuildPrice(data.price),
    },
    {
      label: "維持費",
      value: formatMaintenanceCost(data.maintenance),
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Crossing（踏切）の詳細行
 */
function buildCrossingRows(data: CrossingData): TableRow[] {
  return [
    {
      label: "軌道タイプ1",
      value: formatWaytype(data.waytype1),
    },
    {
      label: "最高速度1",
      value: formatSpeed(data.topspeed1),
    },
    {
      label: "軌道タイプ2",
      value: formatWaytype(data.waytype2),
    },
    {
      label: "最高速度2",
      value: formatSpeed(data.topspeed2),
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Citycar（市内車両）の詳細行
 */
function buildCitycarRows(data: CitycarData): TableRow[] {
  return [
    {
      label: "出現確率（重み）",
      value: data.distribution_weight,
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * Pedestrian（歩行者）の詳細行
 */
function buildPedestrianRows(data: PedestrianData): TableRow[] {
  return [
    {
      label: "出現確率（重み）",
      value: data.distribution_weight,
    },
    {
      label: "登場年月",
      value: formatDate(data.intro_date),
    },
    {
      label: "引退年月",
      value: formatDate(data.retire_date),
    },
  ];
}

/**
 * 汎用的な詳細行（不明なタイプの場合）
 */
function buildGenericRows(data: Record<string, unknown>): TableRow[] {
  return Object.entries(data)
    .filter(([key]) => !key.endsWith("_str")) // *_str系は除外（元データと重複）
    .map(([label, value]) => ({ label, value: String(value) }));
}
export default PakGenericMetadata;
