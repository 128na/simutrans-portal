/**
 * 建物タイプの日本語翻訳マッピング
 */

/**
 * 建物タイプの翻訳
 */
const BUILDING_TYPE_TRANSLATIONS: Record<string, string> = {
  Unknown: "不明",
  "City Attraction": "都市アトラクション",
  "Land Attraction": "観光名所",
  Monument: "記念碑",
  Factory: "工場",
  "Town Hall": "市役所",
  Others: "その他",
  Headquarters: "本社",
  Dock: "ドック",
  Depot: "車庫",
  Stop: "停留所",
  "Stop Extension": "停留所拡張",
  "Flat Dock": "平坦ドック",
  Residential: "住宅",
  Commercial: "商業",
  Industrial: "工業",
};

/**
 * システムタイプの翻訳
 */
const SYSTEM_TYPE_TRANSLATIONS: Record<string, string> = {
  flat: "平坦",
  elevated: "高架",
  tram: "路面",
  monorail: "モノレール",
  maglev: "リニア",
  narrowgauge: "狭軌",
};

/**
 * 建物タイプ名を日本語に変換
 */
export function getBuildingTypeName(typeStr: string | number): string {
  const str = String(typeStr);

  // 完全一致
  if (BUILDING_TYPE_TRANSLATIONS[str]) {
    return BUILDING_TYPE_TRANSLATIONS[str];
  }

  // "Unknown Type (数値)" のパターン
  if (str.startsWith("Unknown Type")) {
    return `不明なタイプ ${str.match(/\((\d+)\)/)?.[1] || ""}`.trim();
  }

  return str;
}

/**
 * システムタイプ名を日本語に変換
 */
export function getSystemTypeName(typeStr: string | number): string {
  const str = String(typeStr);

  if (SYSTEM_TYPE_TRANSLATIONS[str]) {
    return SYSTEM_TYPE_TRANSLATIONS[str];
  }

  return str;
}

/**
 * Enables フラグを日本語に変換
 */
export function getEnablesString(enablesStr: string): string {
  const translations: Record<string, string> = {
    Passengers: "旅客",
    Mail: "郵便",
    Goods: "貨物",
    None: "なし",
  };

  return enablesStr
    .split(", ")
    .map((item) => translations[item.trim()] || item)
    .join("、");
}

/**
 * 配置場所タイプの翻訳
 */
const PLACEMENT_TRANSLATIONS: Record<string, string> = {
  Land: "陸地",
  Water: "水上",
  City: "市街地",
};

/**
 * 配置場所タイプを日本語に変換
 */
export function getPlacementName(placementStr: string | number): string {
  const str = String(placementStr);
  return PLACEMENT_TRANSLATIONS[str] || str;
}

/**
 * 気候名の翻訳
 */
const CLIMATE_TRANSLATIONS: Record<string, string> = {
  water_climate: "水上",
  desert_climate: "砂漠",
  tropic_climate: "熱帯",
  mediterran_climate: "地中海",
  temperate_climate: "温帯",
  tundra_climate: "ツンドラ",
  rocky_climate: "岩石",
  arctic_climate: "北極",
};

/**
 * 気候名を日本語に変換（カンマ区切りの複数気候に対応）
 */
export function getClimateNames(climateStr: string): string {
  return climateStr
    .split(", ")
    .map((item) => CLIMATE_TRANSLATIONS[item.trim()] || item)
    .join("、");
}
