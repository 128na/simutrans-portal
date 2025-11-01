import { createRoot } from "react-dom/client";
import { match } from "ts-pattern";
import { JSX } from "react";

const app = document.getElementById("app-article-edit");

if (app) {
  const user = JSON.parse(app.dataset.user || "{}") as User.Listing;
  const article = JSON.parse(app.dataset.article || "{}") as Article.Editing;

  const App = () => {
    return match<PostType>(article.post_type)
      .returnType<JSX.Element>()
      .with("page", () => <div>page</div>)
      .with("markdown", () => <div>markdown</div>)
      .with("addon-post", () => <div>addon-post</div>)
      .with("addon-introduction", () => <div>addon-introduction</div>)
      .exhaustive();
  };

  createRoot(app).render(<App />);
}
