import { createRoot } from "react-dom/client";
import { ArticleBase } from "../../features/articles/components/ArticleBase";
import { ErrorBoundary } from "../../components/ErrorBoundary";

const app = document.getElementById("app-article-show");

if (app) {
  const App = () => {
    const article = JSON.parse(
      document.getElementById("data-article")?.textContent || "{}"
    );

    return <ArticleBase article={article} />;
  };

  createRoot(app).render(
    <ErrorBoundary name="ArticleShowPage">
      <App />
    </ErrorBoundary>
  );
}
