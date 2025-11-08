import { createRoot } from "react-dom/client";
import { match } from "ts-pattern";
import { JSX, useEffect } from "react";
import { AddonIntroduction } from "./features/articles/postType/AddonIntroduction";
import { AddonPost } from "./features/articles/postType/AddonPost";
import { Markdown } from "./features/articles/postType/Markdown";
import { Page } from "./features/articles/postType/Page";
import Button from "./components/ui/Button";
import Checkbox from "./components/ui/Checkbox";
import Label from "./components/ui/Label";
import { useArticleEditor } from "./state/useArticleEditor";

const el = (id: string) => document.getElementById(id);
const app = el("app-article-edit");

if (app) {
  const App = () => {
    const init = useArticleEditor((s) => s.init);

    useEffect(() => {
      init({
        user: JSON.parse(el("data-user")!.textContent!),
        article: JSON.parse(el("data-article")!.textContent!),
        attachments: JSON.parse(el("data-attachments")!.textContent!),
        tags: JSON.parse(el("data-tags")!.textContent!),
        categories: JSON.parse(el("data-categories")!.textContent!),
        relationalArticles: JSON.parse(
          el("data-relational-articles")!.textContent!,
        ),
        shouldNotfy: false,
      });
    }, []);

    const article = useArticleEditor((s) => s.article);
    const shouldNotfy = useArticleEditor((s) => s.shouldNotfy);
    const updateShouldNotify = useArticleEditor((s) => s.updateShouldNotify);

    if (!article || !article.post_type) return null;

    return (
      <>
        {match<PostType>(article.post_type)
          .returnType<JSX.Element>()
          .with("page", () => <Page />)
          .with("markdown", () => <Markdown />)
          .with("addon-post", () => <AddonPost />)
          .with("addon-introduction", () => <AddonIntroduction />)
          .exhaustive()}
        {article.status === "publish" && (
          <div className="mt-2">
            <Label>公開時のSNS通知</Label>
            <Checkbox
              checked={shouldNotfy}
              onChange={() => updateShouldNotify(!shouldNotfy)}
            >
              通知する
            </Checkbox>
          </div>
        )}
        <div className="mt-2">
          <Button>保存</Button>
        </div>
      </>
    );
  };

  createRoot(app).render(<App />);
}
