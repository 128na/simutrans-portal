import { createRoot } from "react-dom/client";
import { LatestArticleCardList } from "../../features/articles/components/LatestArticleCardList";
import { AppWrapper } from "../../components/AppWrapper";

/**
 * トップ・/latest の新着カード。1 ページに複数（/latest は pak ごと）あるので、
 * data-latest-cards の要素ごとに、指定された id の埋め込みデータ（保存データから作ったカード）を描画する
 */
const containers = document.querySelectorAll<HTMLElement>(
  "[data-latest-cards]"
);

if (containers.length > 0) {
  const isAuthenticatedElement = document.getElementById(
    "data-is-authenticated"
  );
  const isAuthenticated = isAuthenticatedElement
    ? JSON.parse(isAuthenticatedElement.textContent || "false") === true
    : false;

  containers.forEach((container) => {
    const articles: Article.LatestCard[] = JSON.parse(
      document.getElementById(container.dataset.latestCards ?? "")
        ?.textContent || "[]"
    );

    createRoot(container).render(
      <AppWrapper boundaryName="LatestCardsPage">
        <LatestArticleCardList
          articles={articles}
          isAuthenticated={isAuthenticated}
        />
      </AppWrapper>
    );
  });
}
