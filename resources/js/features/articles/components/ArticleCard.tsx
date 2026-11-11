import type { ReactNode } from "react";
import { Categories } from "./Categories";
import { AddToMyListButton } from "../../mylist/AddToMyList";

export type ArticleCardSort = "modified" | "published";

type Props = {
  article: Article.Card;
  sort?: ArticleCardSort;
  isAuthenticated?: boolean;
  /**
   * カード下部の操作欄に足す要素（まとめてダウンロードの「選ぶ」など）
   */
  actions?: ReactNode;
};

/**
 * アドオン投稿（ダウンロードできる）なら true、アドオン紹介（掲載先へ移動）なら false
 */
export const isDownloadable = (article: Article.Card): boolean =>
  Boolean(article.download_url);

const dateOnly = (value: string | null): string => value?.slice(0, 10) ?? "";

const DateLabel = ({
  article,
  sort,
}: {
  article: Article.Card;
  sort: ArticleCardSort;
}) => {
  if (sort === "published" || !article.modified_at) {
    return <>公開 {dateOnly(article.published_at)}</>;
  }
  return <>更新 {dateOnly(article.modified_at)}</>;
};

/**
 * 入手導線。既存の CV 経路（download_url / addon_page_url）をそのまま使う
 */
const AcquisitionLink = ({ article }: { article: Article.Card }) => {
  if (article.download_url) {
    return (
      <a
        className="v2-button v2-button-md v2-button-primary inline-block font-semibold"
        href={article.download_url}
        target="_blank"
        rel="noopener noreferrer"
        aria-label={`${article.title} をダウンロード`}
      >
        ダウンロード
      </a>
    );
  }

  if (article.addon_page_url) {
    return (
      <a
        className="v2-button-outline v2-button-md v2-button-primary-outline inline-block font-semibold"
        href={article.addon_page_url}
        aria-label={`${article.title} の掲載先へ`}
      >
        掲載先へ ↗
      </a>
    );
  }

  return null;
};

export const ArticleCard = ({
  article,
  sort = "modified",
  isAuthenticated = false,
  actions,
}: Props) => {
  const paks = article.categories.filter((c) => c.type === "pak");
  const addons = article.categories.filter((c) => c.type === "addon");

  return (
    <article className="flex flex-col overflow-hidden rounded-lg border border-c-border bg-white">
      <a
        href={article.url}
        aria-label={article.title}
        className="block aspect-video bg-c-surface"
      >
        <img
          className="h-full w-full object-cover"
          src={article.thumbnail}
          alt=""
          loading="lazy"
        />
      </a>
      <div className="flex flex-1 flex-col gap-2 p-3">
        <h3 className="text-body leading-snug line-clamp-2">
          <a href={article.url} className="v2-title-link">
            {article.title}
          </a>
        </h3>
        <div className="flex flex-wrap gap-1">
          <Categories categories={paks} />
          <Categories categories={addons} />
          {article.addon_page_url && (
            <span className="v2-tag">掲載先で入手</span>
          )}
        </div>
        <div className="text-meta text-c-sub break-all">
          <a href={article.user.url} className="text-c-sub hover:text-c-link">
            {article.user.name}
          </a>
          {" ・ "}
          <DateLabel article={article} sort={sort} />
        </div>
        <div className="mt-auto flex flex-wrap items-center gap-2 pt-1">
          <AcquisitionLink article={article} />
          {isAuthenticated && <AddToMyListButton articleId={article.id} />}
          {actions}
        </div>
      </div>
    </article>
  );
};
