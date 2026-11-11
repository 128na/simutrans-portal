import type { ReactNode } from "react";
import { ArticleCard, type ArticleCardSort } from "./ArticleCard";

type Props = {
  articles: Article.Card[];
  sort?: ArticleCardSort;
  isAuthenticated?: boolean;
  /**
   * カードごとに操作欄へ足す要素（まとめてダウンロードの「選ぶ」など）
   */
  renderActions?: (article: Article.Card) => ReactNode;
};

/**
 * 公開側のアドオン一覧（カード表示）。PC 幅で 4 列、スマホでは 1 列
 */
export const ArticleCardList = ({
  articles,
  sort = "modified",
  isAuthenticated = false,
  renderActions,
}: Props) => {
  if (articles.length === 0) {
    return (
      <div className="v2-text-sub">条件に合う記事が見つかりませんでした。</div>
    );
  }

  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      {articles.map((article) => (
        <ArticleCard
          key={article.id}
          article={article}
          sort={sort}
          isAuthenticated={isAuthenticated}
          actions={renderActions?.(article)}
        />
      ))}
    </div>
  );
};
