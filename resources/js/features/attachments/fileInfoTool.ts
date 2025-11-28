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
 * app\Services\FileInfo\Extractors\Pak\WayTypeConverter.php
 */
type WayType =
  | "road"
  | "track"
  | "water"
  | "air"
  | "monorail"
  | "maglev"
  | "tram"
  | "narrowgauge"
  | "powerline";

const objectTypeCategoryMap = {
  good: "industrial-tools",
  factory: "industrial-tools",
  citycar: "others",
  pedestrian: "others",
} as const satisfies Partial<Record<ObjectType, CategorySlug>>;

const wayCategoryMap = {
  road: "road-vehicles",
  track: "trains",
  water: "ships",
  air: "aircrafts",
  monorail: "monorail-vehicles",
  maglev: "maglev-vehicles",
  tram: "tram-vehicle",
  narrowgauge: "narrow-gauge-vahicle",
} as const satisfies Partial<Record<WayType, CategorySlug>>;

const wayBuildingCategoryMap = {
  road: "road-tools",
  track: "rail-tools",
  water: "seaport-tools",
  air: "airport-tools",
  monorail: "monorail-tools",
  maglev: "maglev-tools",
  tram: "tram-tools",
  narrowgauge: "narrow-gauge-tools",
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
      console.log(obj);
      // 対応するカテゴリがあれば追加していく
      const objectType = obj.objectType as ObjectType;
      // objタイプ
      if (objectType in objectTypeCategoryMap) {
        add(
          objectTypeCategoryMap[
            objectType as keyof typeof objectTypeCategoryMap
          ],
          addons,
          selected
        );
      }
      // 軌道
      if (objectType === "way" && obj.wayData) {
        const wayType = obj.wayData.wtyp_str as WayType;
        if (wayType in wayCategoryMap) {
          add(
            wayCategoryMap[wayType as keyof typeof wayCategoryMap],
            addons,
            selected
          );
        }
      }
      // 建物
      if (objectType === "building" && obj.buildingData) {
        const wayType = obj.buildingData.waytype_str as WayType;
        if (wayType in wayCategoryMap) {
          add(
            wayCategoryMap[wayType as keyof typeof wayCategoryMap],
            addons,
            selected
          );
        }
      }

      // 車両
      if (objectType === "vehicle" && obj.vehicleData) {
        // todo
      }
    });
  });

  return Array.from(selected);
};

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
