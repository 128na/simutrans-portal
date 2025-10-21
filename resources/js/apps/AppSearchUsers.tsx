import { useState } from "react";
import { createRoot } from "react-dom/client";
import { SelectableSearch } from "./components/SelectableSearch";

const app = document.getElementById("app-search-users");

if (app) {
  const initialUserIds = JSON.parse(app.dataset.userIds || "[]").map(Number);
  const options = JSON.parse(app.dataset.options || "[]");

  const App = () => {
    const [userIds, setUserIds] = useState<number[]>(initialUserIds);

    return (
      <SelectableSearch
        options={options}
        selectedIds={userIds}
        labelKey="name"
        onChange={setUserIds}
      />
    );
  };

  createRoot(app).render(<App />);
}
