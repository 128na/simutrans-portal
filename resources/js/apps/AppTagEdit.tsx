import { createRoot } from "react-dom/client";
import { TagTable } from "./components/TagTable";
import { TagModal } from "./components/TagModal";
import { useState } from "react";

const app = document.getElementById("app-tag-edit");

if (app) {
  const tags = JSON.parse(app.dataset.tags || "[]") as Tag[];

  const App = () => {
    const [selectedTag, setSelectedTag] = useState<Tag | NewTag | null>(null);
    const updateTag = (tag: Tag) => {
      const index = tags.findIndex((t) => t.id === tag.id);
      if (index !== -1) {
        tags[index] = tag;
      } else {
        tags.push(tag);
      }
      setSelectedTag(null);
    };
    return (
      <>
        <TagTable
          tags={tags}
          limit={15}
          onClick={(tag) => setSelectedTag(tag)}
        />
        <TagModal
          tag={selectedTag}
          onClose={() => setSelectedTag(null)}
          onSave={updateTag}
        />
      </>
    );
  };

  createRoot(app).render(<App />);
}
