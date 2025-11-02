import { createRoot } from "react-dom/client";
import { match } from "ts-pattern";
import { JSX, useState } from "react";
import { AddonIntroduction } from "./features/articles/postType/AddonIntroduction";
import { AddonPost } from "./features/articles/postType/AddonPost";
import { Markdown } from "./features/articles/postType/Markdown";
import { Page } from "./features/articles/postType/Page";

const app = document.getElementById("app-article-edit");

if (app) {
  const user = JSON.parse(
    document.getElementById("data-user")?.textContent || "{}",
  ) as User.Listing;
  const attachments = JSON.parse(
    document.getElementById("data-attachments")?.textContent || "[]",
  );
  const categories = JSON.parse(
    document.getElementById("data-categories")?.textContent || "[]",
  );
  const tags = JSON.parse(
    document.getElementById("data-tags")?.textContent || "[]",
  );
  const relationalArticles = JSON.parse(
    document.getElementById("data-relational-articles")?.textContent || "[]",
  );

  console.log({
    user,
    attachments,
    categories,
    tags,
    relationalArticles,
  });

  const App = () => {
    const [article, setArticle] = useState<Article.Editing>(
      JSON.parse(document.getElementById("data-article")?.textContent || "[]"),
    );
    console.log({ article });
    const props = {
      ...{ article, user, attachments, categories, tags, relationalArticles },
      onChange: setArticle,
    };
    return match<PostType>(article.post_type)
      .returnType<JSX.Element>()
      .with("page", () => <Page {...props} />)
      .with("markdown", () => <Markdown {...props} />)
      .with("addon-post", () => <AddonPost {...props} />)
      .with("addon-introduction", () => <AddonIntroduction {...props} />)
      .exhaustive();
  };

  createRoot(app).render(<App />);
}
