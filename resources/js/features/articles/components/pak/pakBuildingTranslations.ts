/**
 * 建物タイプの日本語翻訳マッピング
 */

import {
  BUILDING_TYPE_TRANSLATIONS,
  CLIMATE_TRANSLATIONS,
  PLACEMENT_TRANSLATIONS,
  SYSTEM_TYPE_TRANSLATIONS,
} from "./pakConstants";

/**
 * 建物タイプ名を日本語に変換
 */
export function getBuildingTypeName(type: number | undefined): string {
  if (type === undefined) return "";
  return BUILDING_TYPE_TRANSLATIONS[type] || `不明なタイプ (${type})`;
}

/**
 * システムタイプ名を日本語に変換
 */
export function getSystemTypeName(type: number | undefined): string {
  if (type === undefined) return "";
  return SYSTEM_TYPE_TRANSLATIONS[type] || String(type);
}

/**
 * Enables フラグを日本語に変換
 */
export function getEnablesString(enables: number | undefined): string {
  if (enables === undefined) return "";

  const flags: string[] = [];
  if (enables & 0x01) flags.push("旅客");
  if (enables & 0x02) flags.push("郵便");
  if (enables & 0x04) flags.push("貨物");
  if (flags.length === 0) flags.push("なし");
  return flags.join("、");
}

/**
 * 配置場所タイプを日本語に変換
 */
export function getPlacementName(placement: number | undefined): string {
  if (placement === undefined) return "";
  return PLACEMENT_TRANSLATIONS[placement] || String(placement);
}

/**
 * 気候名を日本語に変換（ビットフラグから複数気候に対応）
 */
export function getClimateNames(climates: number | undefined): string {
  if (climates === undefined) return "";

  const names: string[] = [];
  for (let i = 0; i < 8; i++) {
    if (climates & (1 << i)) {
      names.push(CLIMATE_TRANSLATIONS[i]);
    }
  }
  return names.join("、") || "なし";
}
