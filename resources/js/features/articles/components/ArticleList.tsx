import { Categories } from "./Categories";
import { ProfileShow } from "../../user/ProfileShow";
import { Tags } from "./Tags";
import { AddToMyListButton } from "../../mylist/AddToMyList";

type Props = {
  articles: Article.List[];
  isAuthenticated?: boolean;
};

export const ArticleList = ({ articles, isAuthenticated = false }: Props) => {
  if (articles.length === 0) {
    return <div className="v2-text-sub">記事が見つかりませんでした。</div>;
  }

  return (
    <div className="v2-page-content-area-lg">
      {articles.map((article) => (
        <article key={article.id}>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="shrink-0">
              <a href={article.url}>
                <img
                  className="w-full sm:w-80 h-45 object-cover rounded-lg shadow-lg"
                  src={article.thumbnail}
                  alt=""
                />
              </a>
            </div>
            <div className="flex flex-col gap-2">
              <div className="text-sm v2-text-sub">
                {article.modified_at} ({article.published_at} 投稿)
              </div>
              <h3 className="v2-text-h3">
                <a href={article.url} className="v2-hover-text-sub">
                  {article.title}
                </a>
              </h3>
              <div className="text-sm v2-text-sub line-clamp-3 break-all">
                {article.description}
              </div>
              <div className="text-xs flex flex-wrap gap-2">
                <Categories categories={article.categories} />
                <Tags tags={article.tags} />
              </div>
              <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <ProfileShow user={article.user} />
                <div className="flex flex-wrap gap-2 sm:justify-end">
                  {article.download_url && (
                    <a
                      className="v2-button v2-button-md v2-button-primary"
                      href={article.download_url}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      ダウンロード
                    </a>
                  )}
                  {article.addon_page_url && (
                    <a
                      href={article.addon_page_url}
                      className="v2-button v2-button-md v2-button-primary"
                    >
                      掲載ページ
                    </a>
                  )}
                  {isAuthenticated && article.id && (
                    <AddToMyListButton articleId={article.id} />
                  )}
                </div>
              </div>
            </div>
          </div>
        </article>
      ))}
    </div>
  );
};
