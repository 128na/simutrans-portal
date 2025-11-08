import { createRoot } from "react-dom/client";
import { match } from "ts-pattern";
import { JSX, useEffect, useState } from "react";
import { SelectPostType } from "./features/articles/SelectPostType";
import { AddonIntroduction } from "./features/articles/postType/AddonIntroduction";
import { AddonPost } from "./features/articles/postType/AddonPost";
import { Markdown } from "./features/articles/postType/Markdown";
import { Page } from "./features/articles/postType/Page";
import { createArticle } from "./features/articles/articleUtil";
import { useArticleEditor } from "./state/useArticleEditor";
import Label from "./components/ui/Label";
import Checkbox from "./components/ui/Checkbox";
import Button from "./components/ui/Button";

const el = (id: string) => document.getElementById(id);
const app = el("app-article-create");

if (app) {
  const App = () => {
    const init = useArticleEditor((s) => s.init);

    useEffect(() => {
      init({
        user: JSON.parse(el("data-user")!.textContent!),
        article: {} as Article.Editing,
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
    const user = useArticleEditor((s) => s.user);

    const [postType, setPostType] = useState<PostType | null>(null);
    // postTypeが変更されたときのみ初期化
    useEffect(() => {
      if (postType) {
        useArticleEditor.setState((state) => {
          state.article = createArticle(postType, user);
        });
      }
    }, [postType]);

    if (!postType || !article || !article.post_type) {
      return <SelectPostType onClick={setPostType} />;
    }

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
