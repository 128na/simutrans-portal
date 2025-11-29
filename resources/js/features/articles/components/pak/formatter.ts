import { RoadsignData } from "@/types/models";
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
  return `${(gear / 100).toFixed(1)}`;
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
  return `${price.toLocaleString()} Cr`;
};

export const formatBuildPrice = (price: number | undefined): string => {
  if (price === undefined) {
    return "";
  }
  return formatPrice(price * 100);
};

export const formatRunningCost = (
  cost: number | undefined,
  digit: number | undefined = 2
): string => {
  if (cost === undefined) {
    return "";
  }
  return `${(cost / 100).toLocaleString(undefined, {
    minimumFractionDigits: digit,
    maximumFractionDigits: digit,
  })} Cr/km`;
};

export const formatMaintenanceCost = (
  cost: number | undefined,
  digit: number | undefined = 2
): string => {
  if (cost === undefined) {
    return "";
  }
  return `${(cost / 100).toLocaleString(undefined, {
    minimumFractionDigits: digit,
    maximumFractionDigits: digit,
  })} Cr/月`;
};

export const formatGoodCategory = (catg: number | undefined): string => {
  if (catg === undefined) {
    return "";
  }
  return GOOD_CATEGORY_TRANSLATIONS[catg] || "";
};

export const formatGoodMetric = (
  weight_per_unit: number | undefined,
  metric: string | undefined
): string => {
  if (weight_per_unit === undefined || metric === undefined) {
    return "";
  }
  return `${weight_per_unit.toLocaleString()} kg/${metric}`;
};

export const formatGoodPrice = (
  base_value: number | undefined,
  metric: string | undefined
): string => {
  if (base_value === undefined || metric === undefined) {
    return "";
  }
  return `${base_value.toLocaleString()} Cr/${metric}`;
};

export const formatBoolean = (value: boolean | undefined): string => {
  if (value === undefined) {
    return "";
  }
  return value ? "Yes" : "No";
};

export const formatSignalType = (data: RoadsignData) => {
  return data.is_signal ? "信号" : "標識";
};

export const formatSignalAttribute = (data: RoadsignData) => {
  const attributes: string[] = [];
  if (data.is_one_way) {
    attributes.push("一方通行");
  }
  if (data.is_choose_sign) {
    attributes.push("チューズ");
  }
  if (data.is_private_way) {
    attributes.push("プライベート");
  }
  if (data.is_pre_signal) {
    attributes.push("プレシグナル");
  }
  if (data.is_longblock_signal) {
    attributes.push("ロングブロック");
  }
  if (data.is_priority_signal) {
    attributes.push("プライオリティ");
  }
  if (data.is_end_of_choose) {
    attributes.push("End of choose");
  }
  return attributes.join(", ");
};
