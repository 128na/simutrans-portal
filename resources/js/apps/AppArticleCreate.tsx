import { createRoot } from "react-dom/client";
import { match } from "ts-pattern";
import { useEffect, useState } from "react";
import { SelectPostType } from "./features/articles/SelectPostType";
import { AddonIntroduction } from "./features/articles/postType/AddonIntroduction";
import { AddonPost } from "./features/articles/postType/AddonPost";
import { Markdown } from "./features/articles/postType/Markdown";
import { Page } from "./features/articles/postType/Page";
import { createArticle } from "./features/articles/articleUtil";

const app = document.getElementById("app-article-create");

if (app) {
  const user = JSON.parse(
    document.getElementById("data-user")?.textContent || "{}",
  );
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

  const App = () => {
    const [postType, setPostType] = useState<PostType | null>(null);
    const [article, setArticle] = useState<Article.Editing | null>(null);

    // postTypeが変更されたときのみ初期化
    useEffect(() => {
      if (postType && !article) {
        setArticle(createArticle(postType, user));
      }
    }, [postType]);

    if (!postType || !article) {
      return <SelectPostType onClick={setPostType} />;
    }

    return match(postType)
      .with("page", () => (
        <Page user={user} article={article} onChange={setArticle} />
      ))
      .with("markdown", () => (
        <Markdown user={user} article={article} onChange={setArticle} />
      ))
      .with("addon-post", () => (
        <AddonPost user={user} article={article} onChange={setArticle} />
      ))
      .with("addon-introduction", () => (
        <AddonIntroduction
          user={user}
          article={article}
          onChange={setArticle}
        />
      ))
      .exhaustive();
  };

  createRoot(app).render(<App />);
}
