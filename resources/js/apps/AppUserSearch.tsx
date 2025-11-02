import { createRoot } from "react-dom/client";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";
import { useState } from "react";

const app = document.getElementById("app-search-users");

if (app) {
  const initialUserIds = JSON.parse(app.dataset.userIds || "[]").map(Number);
  const options = JSON.parse(
    document.getElementById("data-options")?.textContent || "{}",
  ).users as SearchableOption[];

  const App = () => {
    const [selectedIds, setSelectedIds] = useState<number[]>(initialUserIds);
    return (
      <div className="p-4">
        {/* hidden inputs for form submission */}
        {selectedIds.map((id) => (
          <input key={id} type="hidden" name="userIds[]" value={id} />
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
