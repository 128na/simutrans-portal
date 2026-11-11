import type { ReactNode } from "react";
import {
  ArticleCard,
  type ArticleCardData,
  type ArticleCardSort,
} from "./ArticleCard";

type Props<T extends ArticleCardData> = {
  articles: T[];
  sort?: ArticleCardSort;
  isAuthenticated?: boolean;
  /**
   * カードごとにタイトルの上の行へ出す要素（新着の「新規／更新」など）
   */
  renderHeading?: (article: T) => ReactNode;
  /**
   * カードごとに操作欄へ足す要素（まとめてダウンロードの「選ぶ」など）
   */
  renderActions?: (article: T) => ReactNode;
};

/**
 * 公開側のアドオン一覧（カード表示）。PC 幅で 4 列、スマホでは 1 列。
 * トップ・/latest の新着も同じ部品で描画し、見た目と列数をそろえる
 */
export const ArticleCardList = <T extends ArticleCardData>({
  articles,
  sort = "modified",
  isAuthenticated = false,
  renderHeading,
  renderActions,
}: Props<T>) => {
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
          heading={renderHeading?.(article)}
          actions={renderActions?.(article)}
        />
      ))}
    </div>
  );
};
