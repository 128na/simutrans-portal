import { createRoot } from "react-dom/client";
import { TagTable } from "./components/TagTable";

const app = document.getElementById("app-tag-edit");

if (app) {
  const tags = JSON.parse(app.dataset.tags || "[]") as Tag[];
  console.log({ tags });

  const App = () => {
    return (
      <TagTable tags={tags} limit={15} onClick={(tag) => console.log(tag)} />
    );
  };

  createRoot(app).render(<App />);
}
