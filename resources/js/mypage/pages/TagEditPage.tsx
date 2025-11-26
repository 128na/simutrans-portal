import { createRoot } from "react-dom/client";
import { useState } from "react";
import { TagEdit } from "../../features/tags/TagEdit";
import { ErrorBoundary } from "../../components/ErrorBoundary";

const app = document.getElementById("app-tag-edit");

if (app) {
  const App = () => {
    const [tags, setTags] = useState<Tag.MypageEdit[]>(
      JSON.parse(document.getElementById("data-tags")?.textContent || "[]")
    );

    return <TagEdit tags={tags} onChangeTags={setTags} />;
  };

  createRoot(app).render(
    <ErrorBoundary name="TagEditPage">
      <App />
    </ErrorBoundary>
  );
}
