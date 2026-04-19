import type {
  BridgeData,
  BuildingData,
  CitycarData,
  CrossingData,
  FactoryData,
  GoodData,
  GroundobjData,
  PedestrianData,
  RoadsignData,
  SoundData,
  TreeData,
  TunnelData,
  VehicleData,
  WayData,
  wayobjData,
} from "@/types/models";
import type { TableRow } from "@/features/articles/components/pak/PakInfoTable";
import {
  getBuildingTypeName,
  getEnablesString,
  getPlacementName,
  getSystemTypeName,
} from "./pakBuildingTranslations";
import {
  formatAxleLoad,
  formatBoolean,
  formatBuildPrice,
  formatClimates,
  formatDate,
  formatEngineType,
  formatFreightType,
  formatGear,
  formatGoodCategory,
  formatGoodMetric,
  formatGoodPrice,
  formatMaintenanceCost,
  formatNum,
  formatPower,
  formatRunningCost,
  formatSignalAttribute,
  formatSignalType,
  formatSpeed,
  formatWeight,
  formatWaytype,
} from "./formatter";

export function buildDetailRows(
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
    case "tree":
      return buildTreeRows(typeData as unknown as TreeData);
    case "groundobj":
      return buildGroundobjRows(typeData as unknown as GroundobjData);
    case "sound":
      return buildSoundRows(typeData as unknown as SoundData);
    case "ground":
    case "menu":
    case "cursor":
    case "symbol":
    case "field":
    case "smoke":
    case "miscimages":
      return [];
    default:
      return buildGenericRows(typeData);
  }
}

/**
 * Unit conversions (based on Simutrans source code):
 * - intro_date/retire_date: stored as (year*12 + month-1) → display as "YYYY年M月"
 * - weight: stored in kg → display in tons (÷1000)
 * - price: stored in 1/100 Cr → display in Cr (×100)
 * - running_cost: stored in 1/100 Cr/km → display in Cr/km (÷100)
 * - maintenance: stored in 1/100 Cr/month → display in Cr/month (÷100)
 * - len: stored in 1/8 tile units → display as-is (raw value)
 * - gear: stored as gear*64 (64=1.00) → display as-is (raw value)
 */
function buildVehicleRows(data: VehicleData): TableRow[] {
  return [
    {
      label: "軌道タイプ",
      value: formatWaytype(data.waytype as number | undefined),
    },
    { label: "エンジンタイプ", value: formatEngineType(data.engine_type) },
    { label: "定員/容量", value: formatNum(data.capacity) },
    { label: "最高速度", value: formatSpeed(data.topspeed) },
    { label: "輸送品目", value: formatFreightType(data.freight_type) },
    { label: "購入価格", value: formatBuildPrice(data.price) },
    { label: "運行費用", value: formatRunningCost(data.running_cost, 2) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    { label: "出力", value: formatPower(data.power) },
    { label: "ギア", value: formatGear(data.gear) },
    { label: "重量", value: formatWeight(data.weight) },
    { label: "軸重", value: formatAxleLoad(data.axle_load) },
    { label: "長さ", value: data.len },
    { label: "乗降時間", value: data.loading_time !== undefined ? `${data.loading_time} ms` : "" },
    { label: "先頭連結可能数", value: data.leader_count },
    { label: "付随連結可能数", value: data.trailer_count },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildWayRows(data: WayData): TableRow[] {
  return [
    { label: "軌道タイプ", value: formatWaytype(data.waytype) },
    { label: "システムタイプ", value: getSystemTypeName(data.styp) },
    { label: "最高速度", value: formatSpeed(data.topspeed) },
    { label: "最大重量", value: formatAxleLoad(data.max_weight) },
    { label: "軸重制限", value: formatAxleLoad(data.axle_load) },
    { label: "建設費", value: formatBuildPrice(data.price) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "下部クリップ",
      value: data.clip_below !== undefined ? formatBoolean(Boolean(data.clip_below)) : "",
    },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildBridgeRows(data: BridgeData): TableRow[] {
  return [
    { label: "軌道タイプ", value: formatWaytype(data.waytype) },
    { label: "最高速度", value: formatSpeed(data.topspeed) },
    { label: "軸重制限", value: formatAxleLoad(data.axle_load) },
    { label: "建設費", value: formatBuildPrice(data.price) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    { label: "最大長", value: data.max_length || "無制限" },
    { label: "最大高さ", value: data.max_height || "無制限" },
    {
      label: "支柱間隔",
      value: data.pillars_every !== undefined
        ? (data.pillars_every === 0 ? "支柱なし" : `${data.pillars_every} タイル`)
        : "",
    },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "下部クリップ",
      value: data.clip_below !== undefined ? formatBoolean(data.clip_below) : "",
    },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildTunnelRows(data: TunnelData): TableRow[] {
  return [
    { label: "軌道タイプ", value: formatWaytype(data.waytype) },
    { label: "最高速度", value: formatSpeed(data.topspeed) },
    { label: "軸重制限", value: formatAxleLoad(data.axle_load) },
    { label: "建設費", value: formatBuildPrice(data.price) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "内部線路",
      value: data.has_way !== undefined ? formatBoolean(data.has_way) : "",
    },
    {
      label: "広口ポータル",
      value: data.broad_portals !== undefined ? formatBoolean(data.broad_portals) : "",
    },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildBuildingRows(data: BuildingData): TableRow[] {
  return [
    { label: "建物タイプ", value: getBuildingTypeName(data.type ?? 0) },
    { label: "軌道タイプ", value: formatWaytype(data.waytype) },
    { label: "取扱い品目", value: getEnablesString(data.enables ?? 0) },
    { label: "レベル", value: data.level || "" },
    { label: "サイズ", value: `${data.size_x || 1} x ${data.size_y || 1}` },
    { label: "レイアウト数", value: data.layouts || "" },
    { label: "建設費", value: formatBuildPrice(data.price) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    {
      label: "地下建設",
      value:
        data.allow_underground !== undefined
          ? (["不可", "地下のみ", "地下/地上"][data.allow_underground] ?? "")
          : "",
    },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildFactoryRows(data: FactoryData): TableRow[] {
  return [
    { label: "生産力", value: data.productivity || "" },
    { label: "生産範囲", value: data.range || "" },
    { label: "出現確率（重み）", value: data.distribution_weight || "" },
    { label: "旅客レベル", value: data.pax_level || "" },
    { label: "配置場所", value: getPlacementName(data.placement) },
    ...(data.input || []).map((item, index) => ({
      label: `入力貨物名${index + 1}`,
      value: item.good,
    })),
    ...(data.output || []).map((item, index) => ({
      label: `出力貨物名${index + 1}`,
      value: item.good,
    })),
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildGoodRows(data: GoodData): TableRow[] {
  return [
    { label: "カテゴリ", value: formatGoodCategory(data.catg) },
    {
      label: "重量",
      value: formatGoodMetric(data.weight_per_unit, data.metric),
    },
    { label: "基本価格", value: formatGoodPrice(data.base_value, data.metric) },
    { label: "速度ボーナス基準", value: data.speed_bonus },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildWayobjRows(data: wayobjData): TableRow[] {
  return [
    { label: "軌道タイプ", value: formatWaytype(data.waytype) },
    {
      label: "電化/非電化",
      value: data.waytype !== data.own_waytype ? "電化" : "非電化",
    },
    { label: "最高速度", value: formatSpeed(data.topspeed) },
    { label: "建設費", value: formatBuildPrice(data.price) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildRoadsignRows(data: RoadsignData): TableRow[] {
  return [
    { label: "軌道タイプ", value: formatWaytype(data.waytype) },
    { label: "最低速度", value: formatSpeed(data.min_speed) },
    { label: "信号/標識", value: formatSignalType(data) },
    { label: "属性", value: formatSignalAttribute(data) },
    { label: "建設費", value: formatBuildPrice(data.price) },
    { label: "維持費", value: formatMaintenanceCost(data.maintenance) },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildCrossingRows(data: CrossingData): TableRow[] {
  return [
    { label: "軌道タイプ1", value: formatWaytype(data.waytype1) },
    { label: "最高速度1", value: formatSpeed(data.topspeed1) },
    { label: "軌道タイプ2", value: formatWaytype(data.waytype2) },
    { label: "最高速度2", value: formatSpeed(data.topspeed2) },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildCitycarRows(data: CitycarData): TableRow[] {
  return [
    { label: "出現確率（重み）", value: data.distribution_weight },
    { label: "最高速度", value: formatSpeed(data.topspeed) },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildPedestrianRows(data: PedestrianData): TableRow[] {
  return [
    { label: "出現確率（重み）", value: data.distribution_weight },
    { label: "登場年月", value: formatDate(data.intro_date) },
    { label: "引退年月", value: formatDate(data.retire_date) },
  ];
}

function buildTreeRows(data: TreeData): TableRow[] {
  return [
    { label: "出現確率（重み）", value: data.distribution_weight },
    { label: "対応気候", value: formatClimates(data.climate_names) },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
  ];
}

function buildGroundobjRows(data: GroundobjData): TableRow[] {
  return [
    { label: "出現確率（重み）", value: data.distribution_weight },
    { label: "対応気候", value: formatClimates(data.climate_names) },
    {
      label: "降雪対応",
      value: formatBoolean((data.number_of_seasons ?? 0) > 0),
    },
    {
      label: "移動速度",
      value: data.speed === 0 ? "静止" : `${data.speed}`,
    },
    { label: "移動地形", value: formatWaytype(data.waytype) },
    { label: "撤去費用", value: formatBuildPrice(data.price) },
    {
      label: "上に木を生やせる",
      value: formatBoolean(data.trees_on_top),
    },
  ];
}

function buildSoundRows(data: SoundData): TableRow[] {
  return [
    { label: "サウンドID", value: data.sound_id },
    { label: "ファイル名", value: data.filename || "" },
  ];
}

function buildGenericRows(data: Record<string, unknown>): TableRow[] {
  return Object.entries(data)
    .filter(([key]) => !key.endsWith("_str"))
    .map(([label, value]) => ({ label, value: String(value) }));
}
