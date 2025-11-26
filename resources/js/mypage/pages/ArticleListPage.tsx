import { createRoot } from "react-dom/client";
import { ArticleTable } from "../../features/articles/ArticleTable";
import { ArticleModal } from "../../features/articles/ArticleModal";
import { useState } from "react";
import { ErrorBoundary } from "../../components/ErrorBoundary";

const app = document.getElementById("app-article-list");

if (app) {
  const user = JSON.parse(
    document.getElementById("data-user")?.textContent || "{}"
  ) as User.MypageShow;
  const articles = JSON.parse(
    document.getElementById("data-articles")?.textContent || "{}"
  ) as Article.MypageShow[];

  const App = () => {
    const [selected, setSelected] = useState<Article.MypageShow | null>(null);
    return (
      <>
        <ArticleTable articles={articles} limit={15} onClick={setSelected} />
        <ArticleModal
          user={user}
          article={selected}
          onClose={() => setSelected(null)}
        />
      </>
    );
  };

  createRoot(app).render(
    <ErrorBoundary name="MypageArticleListPage">
      <App />
    </ErrorBoundary>
  );
}
