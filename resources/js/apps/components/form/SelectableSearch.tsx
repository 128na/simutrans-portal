import { useState } from "react";
import Input from "@/apps/components/ui/Input";
import { twMerge } from "tailwind-merge";

type Props = {
  options: SearchableOption[];
  selectedIds: number[];
  labelKey?: string;
  placeholder?: string;
  onChange?: (selectedIds: number[]) => void;
  render?: (option: SearchableOption) => string;
  className?: string;
};

export const SelectableSearch = ({
  options,
  selectedIds,
  labelKey = "name",
  placeholder = "検索...",
  className,
  onChange,
  render,
}: Props) => {
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

  const selectedItems = options.filter((o) => selectedIds.includes(o.id));
  const filteredItems = options.filter(
    (o) =>
      String(render ? render(o) : o[labelKey])
        ?.toLowerCase()
        .includes(criteria.toLowerCase()) && !selectedIds.includes(o.id),
  );
  return (
    <div className="space-y-4">
      <div>
        <div className="flex flex-wrap gap-2">
          {selectedItems.map((item) => (
            <span
              key={item.id}
              className="bg-brand text-white px-2 py-1 rounded cursor-pointer"
              onClick={() => remove(item.id)}
            >
              {String(item[labelKey])}
              <span className="ml-2">✕</span>
            </span>
          ))}
          {selectedItems.length === 0 && (
            <span className="text-gray-400">（未選択）</span>
          )}
        </div>
      </div>

      <Input
        className={twMerge(className, "mb-2")}
        type="text"
        value={criteria}
        onChange={(e) => setCriteria(e.target.value)}
        placeholder={placeholder}
      />

      <div
        className={twMerge(
          "max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2 bg-white",
          className,
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
              {String(render ? render(o) : o[labelKey])}
            </div>
          ))
        )}
      </div>
    </div>
  );
};
