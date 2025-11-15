import { createRoot } from "react-dom/client";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";
import { useState } from "react";

const app = document.getElementById("app-search-tags");

if (app) {
  const initialTagIds = JSON.parse(app.dataset.tagIds || "[]").map(Number);
  const options = JSON.parse(
    document.getElementById("data-options")?.textContent || "{}",
  ).tags as SearchableOption[];

  const App = () => {
    const [selectedIds, setSelectedIds] = useState<number[]>(initialTagIds);
    return (
      <div className="p-4">
        {/* hidden inputs for form submission */}
        {selectedIds.map((id) => (
          <input key={id} type="hidden" name="tagIds[]" value={id} />
        ))}
        <SelectableSearch
          labelKey="name"
          options={options}
          selectedIds={selectedIds}
          onChange={setSelectedIds}
        />
      </div>
    );
  };

  createRoot(app).render(<App />);
}
