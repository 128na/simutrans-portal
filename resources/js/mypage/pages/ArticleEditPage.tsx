import { createRoot } from "react-dom/client";
import { useEffect } from "react";
import { useArticleEditor } from "../../hooks/useArticleEditor";
import { ArticleEdit } from "../../features/articles/ArticleEdit";

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
          el("data-relational-articles")!.textContent!
        ),
        shouldNotify: false,
        withoutUpdateModifiedAt: false,
        followRedirect: false,
      });
    }, []);

    return <ArticleEdit />;
  };

  createRoot(app).render(<App />);
}
