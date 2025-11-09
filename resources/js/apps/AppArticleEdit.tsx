import { createRoot } from "react-dom/client";
import { useEffect } from "react";
import { useArticleEditor } from "./state/useArticleEditor";
import { ArticleEditor } from "./features/articles/ArticleEditor";

const el = (id: string) => document.getElementById(id);
const app = el("app-article-edit");

if (app) {
  const App = () => {
    const init = useArticleEditor((s) => s.init);

    useEffect(() => {
      init({
        user: JSON.parse(el("data-user")!.textContent!),
        article: JSON.parse(el("data-article")!.textContent!),
        attachments: JSON.parse(el("data-attachments")!.textContent!),
        tags: JSON.parse(el("data-tags")!.textContent!),
        categories: JSON.parse(el("data-categories")!.textContent!),
        relationalArticles: JSON.parse(
          el("data-relational-articles")!.textContent!,
        ),
        shouldNotfy: false,
      });
    }, []);

    return <ArticleEditor />;
  };

  createRoot(app).render(<App />);
}
