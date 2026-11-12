import { createRoot } from "react-dom/client";
import { BulkSelectableArticleList } from "../../features/bulkDownload/BulkSelectableArticleList";
import type { ArticleCardSort } from "../../features/articles/components/ArticleCard";
import { AppWrapper } from "../../components/AppWrapper";

const app = document.getElementById("app-article-cards");

if (app) {
  const App = () => {
    const articles = JSON.parse(
      document.getElementById("data-articles")?.textContent || "[]"
    );
    const isAuthenticatedElement = document.getElementById(
      "data-is-authenticated"
    );
    const isAuthenticated = isAuthenticatedElement
      ? JSON.parse(isAuthenticatedElement.textContent || "false")
      : false;
    // カードの日付の出し方。公開順だけ公開日、それ以外（更新順・人気順・不明な値）は更新日を出す
    const sort: ArticleCardSort =
      app.dataset.sort === "published" ? "published" : "modified";

    return (
      <BulkSelectableArticleList
        articles={articles}
        sort={sort}
        isAuthenticated={isAuthenticated}
      />
    );
  };

  createRoot(app).render(
    <AppWrapper boundaryName="ArticleCardListPage">
      <App />
    </AppWrapper>
  );
}
