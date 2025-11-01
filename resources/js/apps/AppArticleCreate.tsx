import { createRoot } from "react-dom/client";
import { deepCopy } from "./features/articles/articleUtil";
import { match, P } from "ts-pattern";
import { JSX, useState } from "react";
import { SelectPostType } from "./features/articles/SelectPostType";

export type NewArticle = {
  id: null;
  user_id: number;
  title: null | string;
  slug: null | string;
  post_type: null | "addon-post" | "addon-introduction" | "page" | "markdown";
  status: "publish" | "reservation" | "draft" | "trash" | "private";
  published_at: string | null;
  modified_at: string | null;
  created_at: string | null;
  updated_at: string | null;
  contents:
    | null
    | ContentAddonPost
    | ContentAddonIntroduction
    | ContentPage
    | ContentMarkdown;
};
type PostType = NewArticle["post_type"];

const app = document.getElementById("app-article-create");

if (app) {
  const user = JSON.parse(app.dataset.user || "{}");
  const newArticle = {
    id: null,
    user_id: user.id,
    title: null,
    slug: null,
    post_type: null,
    status: "draft",
    contents: null,
    published_at: null,
    modified_at: null,
    created_at: null,
    updated_at: null,
  } as NewArticle;
  const article = deepCopy(newArticle);

  const App = () => {
    const [postType, setPostType] = useState<PostType>(null);

    return match<PostType>(postType)
      .returnType<JSX.Element>()
      .with("page", () => <div>page</div>)
      .with("markdown", () => <div>markdown</div>)
      .with("addon-post", () => <div>addon-post</div>)
      .with("addon-introduction", () => <div>addon-introduction</div>)
      .with(P._, () => <SelectPostType onClick={setPostType} />)
      .exhaustive();
  };

  createRoot(app).render(<App />);
}
