import { createRoot } from "react-dom/client";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";

const app = document.getElementById("app-search-users");

if (app) {
  const initialUserIds = JSON.parse(app.dataset.userIds || "[]").map(Number);
  const options = JSON.parse(app.dataset.options || "[]");

  const App = () => {
    return (
      <SelectableSearch
        name="userIds"
        options={options}
        selectedIds={initialUserIds}
        labelKey="name"
      />
    );
  };

  createRoot(app).render(<App />);
}
