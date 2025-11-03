import { t } from "@/lang/translate";
import { typedKeys } from "./articleUtil";
import Checkbox from "@/apps/components/ui/Checkbox";
import { twMerge } from "tailwind-merge";

type Props = {
  categories: Category.Grouping;
  selected: number[];
  only?: CategoryType[];
  showAdmin?: boolean;
  onSelect: (category: Category.Search) => void;
  typeClassName?: string;
  className?: string;
};

export const SelectCategories = ({
  categories,
  selected,
  only,
  showAdmin,
  typeClassName,
  className,
  onSelect,
}: Props) => {
  const pak128Id = categories["pak"].find((c) => c.slug === "128")?.id;
  const toolIds = categories["addon"]
    .filter((c) => c.slug.includes("-tools"))
    .map((c) => c.id);
  const categoryGroupFilter = (type: CategoryType): boolean => {
    // pak.128 選択時のみ pak128_position を表示
    if (type === "pak128_position") {
      return pak128Id && selected.includes(pak128Id) ? true : false;
    }
    // addon.*-tools 選択時のみ double_slope を表示
    if (type === "double_slope") {
      return selected.some((id) => toolIds.includes(id));
    }
    return only?.includes(type) ?? true;
  };
  const categoryFilter = (c: Category.Search): boolean => {
    // 管理者権限なら全部
    if (showAdmin) {
      return true;
    }
    // 一般ユーザーは通常カテゴリのみ
    return !c.need_admin;
  };

  return typedKeys(categories)
    .filter(categoryGroupFilter)
    .map((type) => (
      <div key={type} className="mb-6">
        <div className={twMerge("text-sm text-gray-900", typeClassName)}>
          {t(`category.type.${type}`)}
        </div>
        {categories[type].filter(categoryFilter).map((category) => (
          <Checkbox
            className={className}
            key={category.id}
            value={category.id}
            checked={selected.includes(category.id)}
            onChange={() => onSelect(category)}
          >
            {t(`category.${category.type}.${category.slug}`)}
          </Checkbox>
        ))}
      </div>
    ));
};
