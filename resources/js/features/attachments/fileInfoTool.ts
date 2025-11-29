import { CategoryGrouping, PakMetadata } from "@/types/models";
import {
  type CategorySlug,
  type ObjectType,
  type WayType,
  OBJECT_TYPE_CATEGORY_MAP,
  WAY_BUILDING_CATEGORY_MAP,
  WAY_CATEGORY_MAP,
} from "@/features/articles/components/pak/pakConstants";

/**
 * readmesの内容を結合して返す
 */
export const getReadmeText = (readmes: Record<string, string[]>) => {
  return Object.entries(readmes)
    .map(
      ([filename, readme]) =>
        `\n--------\n${filename}\n--------\n${readme.join("\n")}\n--------\n`
    )
    .join("\n");
};

/**
 * ファイル情報から適切なカテゴリ一覧を返す
 * @param fileInfo
 */
export const getCategories = (
  paksMetadata: Record<string, PakMetadata[]>,
  categoryGrouping: CategoryGrouping
) => {
  const addons = categoryGrouping.addon;
  const selected = new Set<Category.MypageEdit>();

  Object.keys(paksMetadata).forEach((filename: keyof typeof paksMetadata) => {
    paksMetadata[filename].forEach((obj) => {
      // 対応するカテゴリがあれば追加していく
      const objectType = obj.objectType as ObjectType;
      addByObjType(objectType, addons, selected);

      // 車両
      if (objectType === "vehicle" && obj.vehicleData) {
        const wayType = obj.vehicleData.waytype as WayType;
        return addByWayType(wayType, addons, selected);
      }
      // 建物
      if (objectType === "building" && obj.buildingData) {
        const wayType = obj.buildingData.waytype as WayType;
        return addByWayTypeBuilding(wayType, addons, selected);
      }
      // 軌道
      if (objectType === "way" && obj.wayData) {
        const wayType = obj.wayData.waytype as WayType;
        return addByWayTypeBuilding(wayType, addons, selected);
      }
      // 軌道オブジェクト
      if (objectType === "wayobj" && obj.wayobjData) {
        const wayType = obj.wayobjData.waytype as WayType;
        return addByWayTypeBuilding(wayType, addons, selected);
      }
      // 橋
      if (objectType === "bridge" && obj.bridgeData) {
        const wayType = obj.bridgeData.waytype as WayType;
        return addByWayTypeBuilding(wayType, addons, selected);
      }
      // トンネル
      if (objectType === "tunnel" && obj.tunnelData) {
        const wayType = obj.tunnelData.waytype as WayType;
        return addByWayTypeBuilding(wayType, addons, selected);
      }
      // 標識
      if (objectType === "roadsign" && obj.roadsignData) {
        const wayType = obj.roadsignData.waytype as WayType;
        return addByWayTypeBuilding(wayType, addons, selected);
      }
      // 踏切
      if (objectType === "crossing" && obj.crossingData) {
        const wayType1 = obj.crossingData.waytype1 as WayType;
        addByWayTypeBuilding(wayType1, addons, selected);
        const wayType2 = obj.crossingData.waytype2 as WayType;
        addByWayTypeBuilding(wayType2, addons, selected);
        return;
      }
      // 産業
      if (objectType === "factory" || objectType === "good") {
        return add("industrial-tools", addons, selected);
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
    OBJECT_TYPE_CATEGORY_MAP[
      objectType as keyof typeof OBJECT_TYPE_CATEGORY_MAP
    ],
    categories,
    selected
  );

const addByWayType = (
  wayType: WayType,
  categories: Category.MypageEdit[],
  selected: Set<Category.MypageEdit>
) =>
  add(
    WAY_CATEGORY_MAP[wayType as keyof typeof WAY_CATEGORY_MAP],
    categories,
    selected
  );

const addByWayTypeBuilding = (
  wayType: WayType,
  categories: Category.MypageEdit[],
  selected: Set<Category.MypageEdit>
) =>
  add(
    WAY_BUILDING_CATEGORY_MAP[
      wayType as keyof typeof WAY_BUILDING_CATEGORY_MAP
    ],
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
