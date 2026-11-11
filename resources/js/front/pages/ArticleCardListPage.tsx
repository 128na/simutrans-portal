import { createRoot } from "react-dom/client";
import { ArticleCardList } from "../../features/articles/components/ArticleCardList";
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
    const sort: ArticleCardSort =
      app.dataset.sort === "published" ? "published" : "modified";

    return (
      <ArticleCardList
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
