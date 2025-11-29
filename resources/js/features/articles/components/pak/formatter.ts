import {
  ENGINE_TYPE_TRANSLATIONS,
  FREIGHT_TYPE_TRANSLATIONS,
  GOOD_CATEGORY_TRANSLATIONS,
  WAYTYPE_TRANSLATIONS,
} from "./pakConstants";

/**
 * 日付フォーマット (year*12+month → YYYY/MM)
 */
export function formatDate(value: number | undefined): string {
  if (value === 0 || value === undefined || value >= 999912) {
    return "";
  }

  const year = Math.floor(value / 12);
  const month = (value % 12) + 1;

  return `${year}/${month.toString().padStart(2, "0")}`;
}

/**
 * Waytype番号を日本語名に変換
 */
export const formatWaytype = (type: number | undefined): string => {
  if (type === undefined || type === null) return "(none)";
  return WAYTYPE_TRANSLATIONS[type] || String(type);
};

/**
 * エンジンタイプ番号を日本語名に変換
 */
export const formatEngineType = (type: number | undefined): string => {
  if (type === undefined || type === null) return "(none)";
  return ENGINE_TYPE_TRANSLATIONS[type] || String(type);
};

/**
 * 貨物タイプ文字列を日本語名に変換
 */
export const formatFreightType = (type: string | undefined): string => {
  if (!type) return "(none)";
  return FREIGHT_TYPE_TRANSLATIONS[type] || type;
};

export const formatSpeed = (speed: number | undefined): string => {
  if (speed === undefined) {
    return "";
  }
  return `${speed.toLocaleString()} km/h`;
};

export const formatPower = (power: number | undefined): string => {
  if (power === undefined) {
    return "";
  }
  return `${power.toLocaleString()} kW`;
};

export const formatGear = (gear: number | undefined): string => {
  if (gear === undefined) {
    return "";
  }
  return `${gear.toFixed(1)}`;
};

export const formatWeight = (weight: number | undefined): string => {
  if (weight === undefined) {
    return "";
  }
  return `${(weight / 1000).toFixed(1)} t`;
};

export const formatNum = (num: number | undefined): string => {
  if (num === undefined) {
    return "";
  }
  return `${num.toLocaleString()}`;
};

export const formatPrice = (price: number | undefined): string => {
  if (price === undefined) {
    return "";
  }
  return `${(price * 100).toLocaleString()} Cr`;
};

export const formatRunningCost = (cost: number | undefined): string => {
  if (cost === undefined) {
    return "";
  }
  return `${(cost / 100).toLocaleString()} Cr/km`;
};

export const formatMaintenanceCost = (cost: number | undefined): string => {
  if (cost === undefined) {
    return "";
  }
  return `${(cost / 100).toLocaleString()} Cr/月`;
};

export const formatGoodCategory = (catg: number | undefined): string => {
  if (catg === undefined) {
    return "";
  }
  return GOOD_CATEGORY_TRANSLATIONS[catg] || "";
};
