import { createRoot } from "react-dom/client";
import { ArticleTable } from "../../features/articles/ArticleTable";
import { ArticleModal } from "../../features/articles/ArticleModal";
import { ArticleDeleteModal } from "../../features/articles/ArticleDeleteModal";
import { ArticleConvertModal } from "../../features/articles/ArticleConvertModal";
import { useState } from "react";
import { AppWrapper } from "../../components/AppWrapper";

const app = document.getElementById("app-article-list");

if (app) {
  const user = JSON.parse(
    document.getElementById("data-user")?.textContent || "{}"
  ) as User.MypageShow;
  const initialArticles = JSON.parse(
    document.getElementById("data-articles")?.textContent || "{}"
  ) as Article.MypageShow[];

  type ArticleModalState = {
    type: "info" | "delete" | "convert";
    article: Article.MypageShow;
  };

  const App = () => {
    const [articles, setArticles] =
      useState<Article.MypageShow[]>(initialArticles);
    const [modal, setModal] = useState<ArticleModalState | null>(null);

    return (
      <>
        <ArticleTable
          articles={articles}
          limit={15}
          onClick={(article) => setModal({ type: "info", article })}
        />
        <ArticleModal
          user={user}
          article={modal?.type === "info" ? modal.article : null}
          onClose={() => setModal(null)}
          onDeleteRequest={(article) => setModal({ type: "delete", article })}
          onConvertRequest={(article) => setModal({ type: "convert", article })}
        />
        {modal?.type === "convert" && (
          <ArticleConvertModal
            article={modal.article}
            onClose={() => setModal(null)}
            onSuccess={() => {
              window.location.assign("/mypage/articles");
            }}
          />
        )}
        <ArticleDeleteModal
          article={modal?.type === "delete" ? modal.article : null}
          onClose={() => setModal(null)}
          onSuccess={() => {
            setArticles((prev) =>
              prev.filter((article) => article.id !== modal?.article.id)
            );
            setModal(null);
          }}
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
