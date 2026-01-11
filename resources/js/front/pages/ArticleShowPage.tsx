import { createRoot } from "react-dom/client";
import { ArticleBase } from "../../features/articles/components/ArticleBase";
import { AppWrapper } from "../../components/AppWrapper";

const app = document.getElementById("app-article-show");

if (app) {
  const App = () => {
    const article = JSON.parse(
      document.getElementById("data-article")?.textContent || "{}"
    );
    const isAuthenticated =
      document.getElementById("data-is-authenticated")?.textContent === "true";

    return <ArticleBase article={article} isAuthenticated={isAuthenticated} />;
  };

  createRoot(app).render(
    <AppWrapper boundaryName="ArticleShowPage">
      <App />
    </AppWrapper>
  );
}
