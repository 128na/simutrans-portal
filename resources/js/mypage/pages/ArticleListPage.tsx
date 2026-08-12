import { createRoot } from "react-dom/client";
import { ArticleTable } from "../../features/articles/ArticleTable";
import { ArticleModal } from "../../features/articles/ArticleModal";
import { ArticleDeleteModal } from "../../features/articles/ArticleDeleteModal";
import { useState } from "react";
import { AppWrapper } from "../../components/AppWrapper";

const app = document.getElementById("app-article-list");

if (app) {
  const user = JSON.parse(
    document.getElementById("data-user")?.textContent || "{}"
  ) as User.MypageShow;
  const articles = JSON.parse(
    document.getElementById("data-articles")?.textContent || "{}"
  ) as Article.MypageShow[];

  const App = () => {
    const [selected, setSelected] = useState<Article.MypageShow | null>(null);
    const [deleteTarget, setDeleteTarget] =
      useState<Article.MypageShow | null>(null);
    return (
      <>
        <ArticleTable articles={articles} limit={15} onClick={setSelected} />
        <ArticleModal
          user={user}
          article={selected}
          onClose={() => setSelected(null)}
          onDeleteRequest={(article) => {
            setSelected(null);
            setDeleteTarget(article);
          }}
        />
        <ArticleDeleteModal
          article={deleteTarget}
          onClose={() => setDeleteTarget(null)}
          onSuccess={() => window.location.reload()}
        />
      </>
    );
  };

  createRoot(app).render(
    <AppWrapper boundaryName="MypageArticleListPage">
      <App />
    </AppWrapper>
  );
}
