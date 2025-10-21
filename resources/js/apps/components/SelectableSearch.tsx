import { useState } from "react";

export type Option = {
  id: number;
  [key: string]: any;
};

type Props = {
  name: string;
  options: Option[];
  selectedIds: number[];
  labelKey?: string;
  placeholder?: string;
};

export const SelectableSearch = ({
  name,
  options,
  selectedIds: initialSelected,
  labelKey = "name",
  placeholder = "検索...",
}: Props) => {
  const [criteria, setCriteria] = useState("");
  const [selectedIds, setSelectedIds] = useState<number[]>(initialSelected);

  const add = (id: number) => {
    if (!selectedIds.includes(id)) {
      setSelectedIds([...selectedIds, id]);
    }
  };

  const remove = (id: number) => {
    setSelectedIds(selectedIds.filter((uid) => uid !== id));
  };

  const selectedItems = options.filter((o) => selectedIds.includes(o.id));

  return (
    <div className="p-4 space-y-4">
      {/* hidden inputs for form submission */}
      {selectedIds.map((id) => (
        <input key={id} type="hidden" name={`${name}[]`} value={id} />
      ))}

      <div>
        <div className="mb-2">選択中：</div>
        <div className="flex flex-wrap gap-2">
          {selectedItems.map((item) => (
            <span
              key={item.id}
              className="bg-brand text-white px-2 py-1 rounded cursor-pointer"
              onClick={() => remove(item.id)}
            >
              {item[labelKey]}
              <span className="ml-2">✕</span>
            </span>
          ))}
          {selectedItems.length === 0 && (
            <span className="text-gray-400">（未選択）</span>
          )}
        </div>
      </div>

      <input
        type="text"
        value={criteria}
        onChange={(e) => setCriteria(e.target.value)}
        placeholder={placeholder}
        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
      />

      <div className="max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2 bg-white">
        {options
          .filter(
            (o) =>
              o[labelKey].toLowerCase().includes(criteria.toLowerCase()) &&
              !selectedIds.includes(o.id),
          )
          .map((o) => (
            <div
              key={o.id}
              className="py-1.5 px-2 rounded cursor-pointer hover:bg-gray-100"
              onClick={() => add(o.id)}
            >
              {o[labelKey]}
            </div>
          ))}
      </div>
    </div>
  );
};
