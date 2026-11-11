import { createRoot } from "react-dom/client";
import { LatestArticleCardList } from "../../features/articles/components/LatestArticleCardList";
import { AppWrapper } from "../../components/AppWrapper";

/**
 * 埋め込みデータは描画の中で読み、壊れていてもその区画だけをエラー表示にする（他の区画は描画を続ける）
 */
const LatestCards = ({
  dataId,
  isAuthenticated,
}: {
  dataId: string;
  isAuthenticated: boolean;
}) => {
  const articles: Article.LatestCard[] = JSON.parse(
    document.getElementById(dataId)?.textContent || "[]"
  );

  return (
    <LatestArticleCardList
      articles={articles}
      isAuthenticated={isAuthenticated}
    />
  );
};

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
    createRoot(container).render(
      <AppWrapper boundaryName="LatestCardsPage">
        <LatestCards
          dataId={container.dataset.latestCards ?? ""}
          isAuthenticated={isAuthenticated}
        />
      </AppWrapper>
    );
  });
}
