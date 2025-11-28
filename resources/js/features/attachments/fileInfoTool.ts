import { CategoryGrouping, FileInfoMypageEdit } from "@/types/models";

/**
 * readmesの内容を結合して返す
 */
export const getReadmeText = (fileInfo: FileInfoMypageEdit) => {
  return Object.entries(fileInfo.data.readmes ?? {})
    .map(
      ([filename, readme]) =>
        `\n--------\n${filename}\n--------\n${readme.join("\n")}\n--------\n`
    )
    .join("\n");
};

/**
 * app\Services\FileInfo\Extractors\Pak\ObjectTypeConverter.php
 */
type ObjectType =
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
 * lang\ja\category.php
 */
type CategorySlug =
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

/**
 * @see app\Services\FileInfo\Extractors\Pak\WayTypeConverter.php
 */
type WayType = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 16 | 128 | 255;

const objectTypeCategoryMap = {
  good: "industrial-tools",
  factory: "industrial-tools",
  citycar: "others",
  pedestrian: "others",
} as const satisfies Partial<Record<ObjectType, CategorySlug>>;

const wayCategoryMap = {
  1: "road-vehicles",
  2: "trains",
  3: "ships",
  16: "aircrafts",
  5: "monorail-vehicles",
  6: "maglev-vehicles",
  7: "tram-vehicle",
  8: "narrow-gauge-vahicle",
} as const satisfies Partial<Record<WayType, CategorySlug>>;

const wayBuildingCategoryMap = {
  1: "road-tools",
  2: "rail-tools",
  3: "seaport-tools",
  16: "airport-tools",
  5: "monorail-tools",
  6: "maglev-tools",
  7: "tram-tools",
  8: "narrow-gauge-tools",
} as const satisfies Partial<Record<WayType, CategorySlug>>;

/**
 * ファイル情報から適切なカテゴリ一覧を返す
 * @param fileInfo
 */
export const getCategories = (
  fileInfo: FileInfoMypageEdit,
  categoryGrouping: CategoryGrouping
) => {
  const meta = fileInfo.data.paks_metadata ?? {};
  const addons = categoryGrouping.addon;
  const selected = new Set<Category.MypageEdit>();

  Object.keys(meta).forEach((filename: keyof typeof meta) => {
    meta[filename].forEach((obj) => {
      // console.log(obj);
      // 対応するカテゴリがあれば追加していく
      const objectType = obj.objectType as ObjectType;
      addByObjType(objectType, addons, selected);

      // 建物
      if (objectType === "building" && obj.buildingData) {
        const wayType = obj.buildingData.waytype as WayType;
        addByWayTypeBuilding(wayType, addons, selected);
      }
      // 軌道
      if (objectType === "way" && obj.wayData) {
        const wayType = obj.wayData.waytype as WayType;
        addByWayType(wayType, addons, selected);
      }
      // 橋
      if (objectType === "bridge" && obj.bridgeData) {
        const wayType = obj.bridgeData.waytype as WayType;
        addByWayType(wayType, addons, selected);
      }
      // トンネル
      if (objectType === "tunnel" && obj.tunnelData) {
        const wayType = obj.tunnelData.waytype as WayType;
        addByWayType(wayType, addons, selected);
      }
      // 車両
      if (objectType === "vehicle" && obj.vehicleData) {
        const wayType = obj.vehicleData.waytype as WayType;
        addByWayType(wayType, addons, selected);
      }
      // 踏切
      if (objectType === "crossing" && obj.crossingData) {
        const wayType1 = obj.crossingData.waytype1 as WayType;
        addByWayType(wayType1, addons, selected);
        const wayType2 = obj.crossingData.waytype2 as WayType;
        addByWayType(wayType2, addons, selected);
      }
    });
  });

  return Array.from(selected);
};

const addByObjType = (
  objectType: ObjectType,
  categories: Category.MypageEdit[],
  selected: Set<Category.MypageEdit>
) =>
  add(
    objectTypeCategoryMap[objectType as keyof typeof objectTypeCategoryMap],
    categories,
    selected
  );

const addByWayType = (
  wayType: WayType,
  categories: Category.MypageEdit[],
  selected: Set<Category.MypageEdit>
) =>
  add(
    wayCategoryMap[wayType as keyof typeof wayCategoryMap],
    categories,
    selected
  );

const addByWayTypeBuilding = (
  wayType: WayType,
  categories: Category.MypageEdit[],
  selected: Set<Category.MypageEdit>
) =>
  add(
    wayBuildingCategoryMap[wayType as keyof typeof wayBuildingCategoryMap],
    categories,
    selected
  );

const add = (
  slug: CategorySlug,
  categories: Category.MypageEdit[],
  selected: Set<Category.MypageEdit>
) => {
  const c = categories.find((c) => c.slug === slug);
  if (c && selected.has(c) === false) {
    selected.add(c);
  }
};
