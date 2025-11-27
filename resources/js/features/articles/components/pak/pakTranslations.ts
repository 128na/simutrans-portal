/**
 * Simutrans pak data translations
 * Based on simutrans/descriptor/vehicle_desc.h and simutrans/simtypes.h
 */

/**
 * Waytype translations
 * enum waytype_t: invalid=-1, ignore=0, road=1, track=2, water=3, overheadlines=4, monorail=5, maglev=6, tram=7, narrowgauge=8, air=16, powerline=128, any=255
 */
export const WAYTYPE_TRANSLATIONS: Record<number | string, string> = {
  "-1": "無効",
  0: "指定なし",
  1: "道路",
  2: "鉄道",
  3: "沿岸",
  4: "架線",
  5: "モノレール",
  6: "リニア",
  7: "市電",
  8: "ナローゲージ",
  16: "航空",
  128: "送電線",
  255: "全て",
  invalid_wt: "無効",
  ignore_wt: "指定なし",
  road_wt: "道路",
  track_wt: "鉄道",
  water_wt: "沿岸",
  overheadlines_wt: "架線",
  monorail_wt: "モノレール",
  maglev_wt: "リニア",
  tram_wt: "市電",
  narrowgauge_wt: "ナローゲージ",
  air_wt: "航空",
  powerline_wt: "送電線",
  any_wt: "全て",
};

/**
 * Engine type translations
 * enum engine_t: unknown=-1, steam=0, diesel=1, electric=2, bio=3, sail=4, fuel_cell=5, hydrogene=6, battery=7
 */
export const ENGINE_TYPE_TRANSLATIONS: Record<number | string, string> = {
  "-1": "不明",
  0: "蒸気",
  1: "ディーゼル",
  2: "電気",
  3: "バイオ",
  4: "帆",
  5: "燃料電池",
  6: "水素",
  7: "バッテリー",
  unknown: "不明",
  steam: "蒸気",
  diesel: "ディーゼル",
  electric: "電気",
  bio: "バイオ",
  sail: "帆",
  fuel_cell: "燃料電池",
  hydrogene: "水素",
  battery: "バッテリー",
};

/**
 * Common freight type translations
 * These are typical values found in Simutrans paksets
 */
export const FREIGHT_TYPE_TRANSLATIONS: Record<string, string> = {
  // Passengers and Mail
  Passagiere: "旅客",
  Passengers: "旅客",
  passengers: "旅客",
  Post: "郵便",
  Mail: "郵便",
  mail: "郵便",

  // Goods
  Waren: "商品",
  Goods: "商品",
  goods: "商品",

  // Resources
  Kohle: "石炭",
  Coal: "石炭",
  coal: "石炭",
  Eisenerz: "鉄鉱石",
  "Iron Ore": "鉄鉱石",
  iron_ore: "鉄鉱石",
  Holz: "木材",
  Wood: "木材",
  wood: "木材",
  Öl: "石油",
  Oil: "石油",
  oil: "石油",

  // Industrial products
  Stahl: "鋼鉄",
  Steel: "鋼鉄",
  steel: "鋼鉄",
  Plastik: "プラスチック",
  Plastic: "プラスチック",
  plastic: "プラスチック",

  // Food
  Nahrung: "食料",
  Food: "食料",
  food: "食料",

  // None/Empty
  None: "なし",
  none: "なし",
  "": "なし",
};

/**
 * Get translated waytype name
 */
export const getWaytypeName = (type: number | string | undefined): string => {
  if (type === undefined || type === null) return "(none)";
  return WAYTYPE_TRANSLATIONS[type] || String(type);
};

/**
 * Get translated engine type name
 */
export const getEngineTypeName = (
  type: number | string | undefined
): string => {
  if (type === undefined || type === null) return "(none)";
  return ENGINE_TYPE_TRANSLATIONS[type] || String(type);
};

/**
 * Get translated freight type name
 */
export const getFreightTypeName = (type: string | undefined): string => {
  if (!type) return "(none)";
  return FREIGHT_TYPE_TRANSLATIONS[type] || type;
};
