import { ArticleCardList } from "./ArticleCardList";
import { dateOnly } from "./ArticleCard";

type Props = {
  articles: Article.LatestCard[];
  isAuthenticated?: boolean;
};

/**
 * 新着としての「新規／更新」を、タイトルの上の行に文字で出す（色だけに頼らず、サムネイルには重ねない）。
 * 更新は更新日を並べ、公開日はカード下の日付（公開 …）で見分ける。新規は日付が 1 つなので下の日付だけにする
 */
const LatestHeading = ({ article }: { article: Article.LatestCard }) => (
  <div className="flex flex-wrap items-center gap-x-1.5 text-meta text-c-sub">
    <span
      className={`rounded border border-c-main px-1.5 font-bold leading-4 ${article.is_updated ? "bg-white text-c-main" : "bg-c-main text-white"}`}
    >
      {article.is_updated ? "更新" : "新規"}
    </span>
    {article.is_updated && article.modified_at && (
      <time dateTime={article.modified_at.replace(/\//g, "-")}>
        {dateOnly(article.modified_at)}
      </time>
    )}
  </div>
);

/**
 * トップ・/latest の新着カード。一覧ページと同じカード・列数で、入手の操作も一覧と同じ（スマホ幅では出さない）。
 * まとめてダウンロード（選ぶ・選択の帯）は一覧ページの機能として、新着には出さない
 */
export const LatestArticleCardList = ({
  articles,
  isAuthenticated = false,
}: Props) => (
  <ArticleCardList
    articles={articles}
    sort="published"
    isAuthenticated={isAuthenticated}
    renderHeading={(article) => <LatestHeading article={article} />}
  />
);
