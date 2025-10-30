import { createRoot } from "react-dom/client";
import { ArticleTable } from "./features/articles/ArticleTable";

const app = document.getElementById("app-article-list");

if (app) {
  const user = JSON.parse(app.dataset.user || "{}");
  const articles = JSON.parse(app.dataset.articles || "[]");
  console.log({ user, articles });

  const onArticleClick = (article: ListingArticle) => {
    console.log({ article });
  };

  const App = () => {
    return (
      <ArticleTable
        user={user}
        articles={articles}
        limit={15}
        onClick={onArticleClick}
      />
    );
  };

  createRoot(app).render(<App />);
}
