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
  return typedKeys(categories)
    .filter((type) => only?.includes(type))
    .map((type) => (
      <div key={type} className="mb-6">
        <div className={twMerge("text-sm text-gray-900", typeClassName)}>
          {t(`category.type.${type}`)}
        </div>
        {categories[type]
          .filter((c) => showAdmin || !c.need_admin)
          .map((category) => (
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
