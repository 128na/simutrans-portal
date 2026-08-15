import { createRoot } from "react-dom/client";
import { useEffect, useState } from "react";
import { SelectPostType } from "../../features/articles/components/SelectPostType";
import { createArticle } from "../../features/articles/utils/articleUtil";
import { useArticleEditor } from "../../hooks/useArticleEditor";
import { ArticleEdit } from "../../features/articles/ArticleEdit";
import { AppWrapper } from "../../components/AppWrapper";

const el = (id: string) => document.getElementById(id);
const app = el("app-article-create");

if (app) {
  const App = () => {
    const init = useArticleEditor((s) => s.init);

    useEffect(() => {
      init({
        user: JSON.parse(el("data-user")!.textContent!),
        article: {} as Article.MypageEdit,
        attachments: JSON.parse(el("data-attachments")!.textContent!),
        tags: JSON.parse(el("data-tags")!.textContent!),
        categories: JSON.parse(el("data-categories")!.textContent!),
        relationalArticles: JSON.parse(
          el("data-relational-articles")!.textContent!
        ),
        shouldNotify: false,
        withoutUpdateModifiedAt: false,
        followRedirect: false,
      });
    }, [init]);

    const article = useArticleEditor((s) => s.article);
    const user = useArticleEditor((s) => s.user);

    const [postType, setPostType] = useState<ArticlePostType | null>(null);
    useEffect(() => {
      if (postType) {
        useArticleEditor.setState((state) => {
          state.article = createArticle(postType, user);
        });
      }
      // postTypeが変更されたときのみ初期化したいため意図的にuserを依存配列から除外している。
      // userはpostType選択より前のinit()で確定済みで、以後この画面では変化しない想定。
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [postType]);

    if (!postType || !article || !article.post_type) {
      return <SelectPostType onClick={setPostType} />;
    }
    return <ArticleEdit />;
  };

  createRoot(app).render(
    <AppWrapper boundaryName="ArticleCreatePage">
      <App />
    </AppWrapper>
  );
}
