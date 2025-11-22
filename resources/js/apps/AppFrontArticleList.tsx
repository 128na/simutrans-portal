import { createRoot } from "react-dom/client";
import { ArticleList } from "./features/frontArticle/ArticleList";

const app = document.getElementById("app-article-list");

if (app) {
  const App = () => {
    const articles = JSON.parse(
      document.getElementById("data-articles")?.textContent || "{}",
    );

    return <ArticleList articles={articles} />;
  };

  createRoot(app).render(<App />);
}
