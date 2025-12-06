import { useState } from "react";
import Input from "@/components/ui/Input";
import { twMerge } from "tailwind-merge";

type SearchableItem = {
  id: number;
};

type Props<T extends SearchableItem = SearchableItem> = {
  options: T[];
  selectedIds: number[];
  labelKey?: keyof T & string;
  placeholder?: string;
  onChange?: (selectedIds: number[]) => void;
  render?: (option: T) => string;
  className?: string;
};

export const SelectableSearch = <T extends SearchableItem = SearchableItem>({
  options,
  selectedIds,
  labelKey = "name" as keyof T & string,
  placeholder = "検索...",
  className,
  onChange,
  render,
}: Props<T>) => {
  const [criteria, setCriteria] = useState("");

  const add = (id: number) => {
    if (onChange) {
      onChange([...selectedIds, id]);
    }
  };

  const remove = (id: number) => {
    if (onChange) {
      onChange(selectedIds.filter((uid) => uid !== id));
    }
  };

  // Helper function to safely get label value
  const getLabel = (item: T): string => {
    if (render) {
      return render(item);
    }
    const value = item[labelKey];
    return String(value ?? "");
  };

  const selectedItems = options.filter((o) => selectedIds.includes(o.id));
  const filteredItems = options.filter(
    (o) =>
      getLabel(o).toLowerCase().includes(criteria.toLowerCase()) &&
      !selectedIds.includes(o.id)
  );
  return (
    <div className="space-y-2">
      <div>
        <div className="flex flex-wrap gap-2">
          {selectedItems.map((item) => (
            <span
              key={item.id}
              className="bg-brand text-white px-2 py-1 rounded cursor-pointer"
              onClick={() => remove(item.id)}
            >
              {getLabel(item)}
              <span className="ml-2">✕</span>
            </span>
          ))}
          {selectedItems.length === 0 && (
            <span className="text-muted">（未選択）</span>
          )}
        </div>
      </div>

      <Input
        className={twMerge(className, "mb-0")}
        type="text"
        value={criteria}
        onChange={(e) => setCriteria(e.target.value)}
        placeholder={placeholder}
      />

      <div
        className={twMerge(
          "max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2 bg-white",
          className
        )}
      >
        {filteredItems.length < 1 ? (
          <div>該当なし</div>
        ) : (
          filteredItems.map((o) => (
            <div
              key={o.id}
              className="py-1.5 px-2 rounded cursor-pointer hover:bg-gray-100"
              onClick={() => add(o.id)}
            >
              {getLabel(o)}
            </div>
          ))
        )}
      </div>
    </div>
  );
};
