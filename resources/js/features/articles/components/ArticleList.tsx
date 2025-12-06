import { Categories } from "./Categories";
import { ProfileShow } from "../../user/ProfileShow";
import { Tags } from "./Tags";

type Props = {
  articles: Article.List[];
};

export const ArticleList = ({ articles }: Props) => {
  if (articles.length === 0) {
    return <div className="text-muted">記事が見つかりませんでした。</div>;
  }

  return (
    <div className="flex flex-col gap-y-12 border-t border-gray-200 pt-6 lg:mx-0">
      {articles.map((article) => (
        <article key={article.id}>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-shrink-0">
              <a href={article.url}>
                <img
                  className="w-full sm:w-80 h-45 object-cover rounded-lg shadow-lg"
                  src={article.thumbnail}
                  alt=""
                />
              </a>
            </div>
            <div className="flex flex-col gap-2">
              <div className="text-sm text-muted">
                {article.modified_at} ({article.published_at} 投稿)
              </div>
              <h3 className="title-md">
                <a href={article.url} className="hover:text-secondary">
                  {article.title}
                </a>
              </h3>
              <div className="text-sm text-secondary line-clamp-3 break-all">
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
