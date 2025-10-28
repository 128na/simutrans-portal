import { createRoot } from "react-dom/client";
import { TagTable } from "./components/TagTable";
import { TagModal } from "./components/TagModal";
import { useState } from "react";

const app = document.getElementById("app-tag-edit");

if (app) {
  const tags = JSON.parse(app.dataset.tags || "[]") as Tag[];

  const App = () => {
    const [selectedTag, setSelectedTag] = useState<Tag | NewTag | null>(null);
    return (
      <>
        <TagTable
          tags={tags}
          limit={15}
          onClick={(tag) => setSelectedTag(tag)}
        />
        <TagModal tag={selectedTag} onClose={() => setSelectedTag(null)} />
      </>
    );
  };

  createRoot(app).render(<App />);
}
