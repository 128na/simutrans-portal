import { createRoot } from "react-dom/client";
import { ArticleBase } from "./features/articleShow/ArticleBase";

const app = document.getElementById("app-article-show");

if (app) {
  const App = () => {
    const article = JSON.parse(
      document.getElementById("data-article")?.textContent || "{}",
    );

    console.log(article);
    return <ArticleBase article={article} />;
  };

  createRoot(app).render(<App />);
}
