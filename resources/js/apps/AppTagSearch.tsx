import { createRoot } from "react-dom/client";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";

const app = document.getElementById("app-search-tags");

if (app) {
  const initialTagIds = JSON.parse(app.dataset.tagIds || "[]").map(Number);
  const options = JSON.parse(
    document.getElementById("data-options")?.textContent || "{}",
  ).tags as SearchOption[];

  const App = () => {
    return (
      <SelectableSearch
        name="tagIds"
        options={options}
        selectedIds={initialTagIds}
        labelKey="name"
      />
    );
  };

  createRoot(app).render(<App />);
}
