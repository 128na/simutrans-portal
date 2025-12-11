import { Categories } from "./Categories";
import { ProfileShow } from "../../user/ProfileShow";
import { Tags } from "./Tags";

type Props = {
  articles: Article.List[];
};

export const ArticleList = ({ articles }: Props) => {
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
              <div className="flex items-center gap-x-3">
                <ProfileShow user={article.user} />
              </div>
            </div>
          </div>
        </article>
      ))}
    </div>
  );
};
