import { t } from "@/utils/translate";
import { typedKeys } from "../utils/articleUtil";
import Checkbox from "@/components/ui/Checkbox";
import { twMerge } from "tailwind-merge";

type Props = {
  categories: Category.Grouping;
  selected: number[];
  only?: CategoryType[];
  showAdmin?: boolean;
  onChange: (categoryIds: number[]) => void;
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
  onChange,
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
  const categoryFilter = (c: Category.MypageEdit): boolean => {
    // 管理者権限なら全部
    if (showAdmin) {
      return true;
    }
    // 一般ユーザーは通常カテゴリのみ
    return !c.need_admin;
  };

  const handle = (categoryId: number) => {
    const idx = selected.findIndex((id) => id === categoryId);

    let next: number[];
    if (idx >= 0) {
      next = selected.filter((id) => id !== categoryId);
    } else {
      next = [...selected, categoryId];
    }

    onChange(next);
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
            onChange={() => handle(category.id)}
          >
            {t(`category.${category.type}.${category.slug}`)}
          </Checkbox>
        ))}
      </div>
    ));
};
