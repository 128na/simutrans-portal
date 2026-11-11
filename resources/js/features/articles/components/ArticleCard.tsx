import type { ReactNode } from "react";
import { Categories } from "./Categories";
import { AddToMyListButton } from "../../mylist/AddToMyList";

export type ArticleCardSort = "modified" | "published";

/**
 * カードに渡せる記事。一覧（Article.Card）と新着（Article.LatestCard。保存データが古いと作者が無い）
 */
export type ArticleCardData = Article.Card | Article.LatestCard;

type Props = {
  article: ArticleCardData;
  sort?: ArticleCardSort;
  isAuthenticated?: boolean;
  /**
   * タイトルの上の行に出す要素（新着の「新規／更新」など）。サムネイルには重ねない
   */
  heading?: ReactNode;
  /**
   * カード下部の操作欄に足す要素（まとめてダウンロードの「選ぶ」など）。
   * スマホ幅で出さないものは渡す側で隠す。未ログインのスマホ幅では操作欄の行ごと出さないので、
   * スマホでも見せたい要素はここに渡さない
   */
  actions?: ReactNode;
};

/**
 * アドオン投稿（ダウンロードできる）なら true、アドオン紹介（掲載先へ移動）なら false
 */
export const isDownloadable = (article: ArticleCardData): boolean =>
  Boolean(article.download_url);

export const dateOnly = (value: string | null): string =>
  value?.slice(0, 10) ?? "";

const DateLabel = ({
  article,
  sort,
}: {
  article: ArticleCardData;
  sort: ArticleCardSort;
}) => {
  if (sort === "published" || !article.modified_at) {
    return <>公開 {dateOnly(article.published_at)}</>;
  }
  return <>更新 {dateOnly(article.modified_at)}</>;
};

/**
 * 入手導線。既存の CV 経路（download_url / addon_page_url）をそのまま使う。
 * ダウンロードは PC で行う前提なので、スマホ幅（sm = 640px 未満。縦向きのスマホが収まり、PC の左右分割やタブレットでは出す）では出さない。
 * display: none で隠すので、キーボードや読み上げでも届かない
 */
const AcquisitionLink = ({ article }: { article: ArticleCardData }) => {
  if (article.download_url) {
    return (
      <a
        className="v2-button v2-button-md v2-button-primary hidden font-semibold sm:inline-block"
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
        className="v2-button-outline v2-button-md v2-button-primary-outline hidden font-semibold sm:inline-block"
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
  heading,
  actions,
}: Props) => {
  const paks = article.categories.filter((c) => c.type === "pak");
  const addons = article.categories.filter((c) => c.type === "addon");

  return (
    <article className="flex flex-col overflow-hidden rounded-lg border border-c-border bg-white">
      {/* overflow-hidden がないと、16:9 より縦長のサムネイルが読み込まれたときに枠ごと伸び、カードの高さがそろわない */}
      <a
        href={article.url}
        aria-label={article.title}
        className="block aspect-video overflow-hidden bg-c-surface"
      >
        <img
          className="h-full w-full object-cover"
          src={article.thumbnail}
          alt=""
          loading="lazy"
        />
      </a>
      <div className="flex flex-1 flex-col gap-1.5 p-2.5">
        {heading}
        {/* v2-title-link の break-all だと英単語の途中で折れるので、単語の切れ目で折る。
            1 語がカード幅より長いときだけ途中で折り、カードを横にはみ出させない。
            v2-title-link は自前のユーティリティで後に出力され break-all が勝つため、! で上書きする。
            break-normal は overflow-wrap も normal にして wrap-anywhere を打ち消すので使わない */}
        <h3 className="text-body leading-snug line-clamp-2">
          <a
            href={article.url}
            className="v2-title-link [word-break:normal]! wrap-anywhere"
          >
            {article.title}
          </a>
        </h3>
        {/* タグは途中で隠さず全部見せる。収まらなければ 2 行以上に折り返す */}
        <div className="flex flex-wrap gap-1">
          <Categories categories={paks} />
          <Categories categories={addons} />
        </div>
        {/* 1 行に収めてカードの高さをそろえる。長い作者名は省略し、日付は途中で折らない */}
        {/* 新着の古い保存データには作者が無いので、そのときは日付だけを出す */}
        <div className="flex min-w-0 text-meta text-c-sub">
          {article.user && (
            <a
              href={article.user.url}
              className="min-w-0 truncate text-c-sub hover:text-c-link"
            >
              {article.user.name}
            </a>
          )}
          <span
            className={`shrink-0 whitespace-nowrap ${article.user ? "pl-1" : ""}`}
          >
            {article.user && "・ "}
            <DateLabel article={article} sort={sort} />
          </span>
        </div>
        {/* スマホ幅では入手・選択の操作を出さないので、マイリストのボタンが無ければ行ごと出さず余白も残さない */}
        <div
          className={`mt-auto flex-wrap items-center gap-1.5 pt-0.5 ${isAuthenticated ? "flex" : "hidden sm:flex"}`}
        >
          <AcquisitionLink article={article} />
          {isAuthenticated && <AddToMyListButton articleId={article.id} />}
          {actions}
        </div>
      </div>
    </article>
  );
};
