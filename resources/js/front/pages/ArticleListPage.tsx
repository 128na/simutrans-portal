import { createRoot } from "react-dom/client";
import { ArticleList } from "../../features/articles/components/ArticleList";
import { AppWrapper } from "../../components/AppWrapper";

const app = document.getElementById("app-article-list");

if (app) {
  const App = () => {
    const articles = JSON.parse(
      document.getElementById("data-articles")?.textContent || "{}"
    );
    const isAuthenticated =
      document.getElementById("data-is-authenticated")?.textContent === "true";

    return (
      <ArticleList articles={articles} isAuthenticated={isAuthenticated} />
    );
  };

  createRoot(app).render(
    <AppWrapper boundaryName="ArticleListPage">
      <App />
    </AppWrapper>
  );
}
