/**
 * Pak data constants and types
 * Simutrans object definitions and translations
 */

// ============================================================================
// Types
// ============================================================================

/**
 * Object types
 * @see app\Services\FileInfo\Extractors\Pak\ObjectTypeConverter.php
 */
export type ObjectType =
  | "vehicle"
  | "building"
  | "bridge"
  | "tunnel"
  | "way"
  | "wayobj"
  | "roadsign"
  | "crossing"
  | "tree"
  | "good"
  | "factory"
  | "citycar"
  | "pedestrian";

/**
 * Waytype enum
 * enum waytype_t: invalid=-1, ignore=0, road=1, track=2, water=3, overheadlines=4, monorail=5, maglev=6, tram=7, narrowgauge=8, air=16, powerline=128, any=255
 * @see app\Services\FileInfo\Extractors\Pak\WayTypeConverter.php
 */
export type WayType = -1 | 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 16 | 128 | 255;

/**
 * Engine type enum
 * enum engine_t: unknown=-1, steam=0, diesel=1, electric=2, bio=3, sail=4, fuel_cell=5, hydrogene=6, battery=7
 */
export type EngineType = -1 | 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7;

/**
 * Good category enum
 * Categories for goods/cargo classification
 */
export type GoodCategory = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8;

/**
 * Category slug
 * @see lang\ja\category.php
 */
export type CategorySlug =
  | "trains"
  | "rail-tools"
  | "road-tools"
  | "ships"
  | "aircrafts"
  | "road-vehicles"
  | "airport-tools"
  | "industrial-tools"
  | "seaport-tools"
  | "buildings"
  | "monorail-vehicles"
  | "monorail-tools"
  | "maglev-vehicles"
  | "maglev-tools"
  | "narrow-gauge-vahicle"
  | "narrow-gauge-tools"
  | "tram-vehicle"
  | "tram-tools"
  | "scripts"
  | "others"
  | "none";

// ============================================================================
// Translation Maps
// ============================================================================

/**
 * Waytype translations
 */
export const WAYTYPE_TRANSLATIONS: Record<number | string, string> = {
  "-1": "無効",
  0: "指定なし",
  1: "道路",
  2: "鉄道",
  3: "運河",
  4: "架線",
  5: "モノレール",
  6: "リニア",
  7: "市電",
  8: "ナローゲージ",
  16: "航空",
  128: "送電線",
  255: "全て",
};

/**
 * Engine type translations
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
};

/**
 * Freight type translations
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
 * Good category translations
 */
export const GOOD_CATEGORY_TRANSLATIONS: Record<number, string> = {
  0: "特殊貨物",
  1: "小口貨物",
  2: "バルク貨物",
  3: "長尺貨物",
  4: "液体貨物",
  5: "冷蔵貨物",
  6: "旅客",
  7: "郵便",
  8: "なし",
};

/**
 * Building type translations
 */
export const BUILDING_TYPE_TRANSLATIONS: Record<number, string> = {
  1: "都市内施設", // City Attraction
  2: "郊外型施設", // Land Attraction
  7: "本社", // Headquarters
  16: "港", // Dock
  17: "港", // Flat Dock
  33: "車庫", // Depot
  34: "停留所", // Stop
  36: "停留所拡張", // Stop Extension
  37: "住宅", // Residential
  38: "商店", // Commercial
  39: "工場", // Industrial
};

/**
 * System type translations
 */
export const SYSTEM_TYPE_TRANSLATIONS: Record<number, string> = {
  0: "地上", // flat
  1: "高架", // elevated
  2: "市電", // tram
  3: "モノレール", // monorail
  4: "リニア", // maglev
  5: "ナローゲージ", // narrowgauge
};

/**
 * Placement type translations
 */
export const PLACEMENT_TRANSLATIONS: Record<number, string> = {
  0: "陸地", // Land
  1: "沿岸", // Water
  2: "市街地", // City
};

/**
 * Climate translations (bit flags)
 */
export const CLIMATE_TRANSLATIONS: Record<number, string> = {
  0: "海上", // water_climate
  1: "砂漠気候", // desert_climate
  2: "熱帯気候", // tropic_climate
  3: "地中海性気候", // mediterran_climate
  4: "温暖気候", // temperate_climate
  5: "ツンドラ気候", // tundra_climate
  6: "氷雪気候", // rocky_climate
  7: "極気候", // arctic_climate
};

// ============================================================================
// Category Mapping
// ============================================================================

/**
 * ObjectType to CategorySlug mapping
 */
export const OBJECT_TYPE_CATEGORY_MAP = {
  good: "industrial-tools",
  factory: "industrial-tools",
  citycar: "others",
  pedestrian: "others",
} as const satisfies Partial<Record<ObjectType, CategorySlug>>;

/**
 * WayType to vehicle CategorySlug mapping
 */
export const WAY_CATEGORY_MAP = {
  1: "road-vehicles",
  2: "trains",
  3: "ships",
  16: "aircrafts",
  5: "monorail-vehicles",
  6: "maglev-vehicles",
  7: "tram-vehicle",
  8: "narrow-gauge-vahicle",
} as const satisfies Partial<Record<WayType, CategorySlug>>;

/**
 * WayType to building/tool CategorySlug mapping
 */
export const WAY_BUILDING_CATEGORY_MAP = {
  1: "road-tools",
  2: "rail-tools",
  3: "seaport-tools",
  16: "airport-tools",
  5: "monorail-tools",
  6: "maglev-tools",
  7: "tram-tools",
  8: "narrow-gauge-tools",
} as const satisfies Partial<Record<WayType, CategorySlug>>;
