import { createRoot } from "react-dom/client";
import { TagTable } from "@/apps/features/tags/TagTable";
import { TagModal } from "@/apps/features/tags/TagModal";
import { useState } from "react";

const app = document.getElementById("app-tag-edit");

if (app) {
  const tags = JSON.parse(app.dataset.tags || "[]") as Tag[];

  const App = () => {
    const [selected, setSelected] = useState<Tag | NewTag | null>(null);
    const updateTag = (tag: Tag) => {
      const index = tags.findIndex((t) => t.id === tag.id);
      if (index !== -1) {
        tags[index] = tag;
      } else {
        tags.push(tag);
      }
      setSelected(null);
    };
    return (
      <>
        <TagTable tags={tags} limit={15} onClick={(tag) => setSelected(tag)} />
        <TagModal
          tag={selected}
          onClose={() => setSelected(null)}
          onSave={updateTag}
        />
      </>
    );
  };

  createRoot(app).render(<App />);
}
