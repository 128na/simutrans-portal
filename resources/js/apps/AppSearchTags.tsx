import { useState } from "react";
import { createRoot } from "react-dom/client";
import { SelectableSearch } from "./components/SelectableSearch";

const app = document.getElementById("app-search-tags");

if (app) {
  const initialTagIds = JSON.parse(app.dataset.tagIds || "[]").map(Number);
  const options = JSON.parse(app.dataset.options || "[]");

  const App = () => {
    const [tagIds, setTagIds] = useState<number[]>(initialTagIds);

    return (
      <SelectableSearch
        options={options}
        selectedIds={tagIds}
        labelKey="name"
        onChange={setTagIds}
      />
    );
  };

  createRoot(app).render(<App />);
}
