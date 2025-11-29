import { CategoryGrouping, FileInfoMypageEdit } from "@/types/models";
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
export const getReadmeText = (fileInfo: FileInfoMypageEdit) => {
  return Object.entries(fileInfo.data.readmes ?? {})
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
