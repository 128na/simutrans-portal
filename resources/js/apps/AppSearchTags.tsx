import { createRoot } from "react-dom/client";
import { SelectableSearch } from "./components/SelectableSearch";

const app = document.getElementById("app-search-tags");

if (app) {
  const initialTagIds = JSON.parse(app.dataset.tagIds || "[]").map(Number);
  const options = JSON.parse(app.dataset.options || "[]");

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
