import { Categories } from "./Categories";
import { ProfileShow } from "./ProfileShow";
import { Tags } from "./Tags";

type Props = {
  articles: Article.List[];
};

export const ArticleList = ({ articles }: Props) => {
  if (articles.length === 0) {
    return <div className="text-gray-500">記事が見つかりませんでした。</div>;
  }

  return (
    <div className="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
      {articles.map((article) => (
        <article
          className="flex flex-col sm:flex-row gap-6 items-start"
          key={article.id}
        >
          <a href={article.url} className="flex-shrink-0">
            <img
              className="w-full sm:w-80 h-45 object-cover rounded-lg shadow-lg"
              src={article.thumbnail}
              alt=""
            />
          </a>

          <div className="flex flex-col justify-between flex-1">
            <div>
              <div className="text-sm text-gray-500">
                {article.modified_at}({article.published_at} 投稿)
              </div>
              <h3 className="mt-2 text-xl font-semibold text-gray-900 hover:text-gray-600">
                <a href={article.url}>{article.title}</a>
              </h3>
              <p className="mt-3 text-sm text-gray-600 line-clamp-3 break-all">
                {article.description}
              </p>
            </div>
            <div className="text-xs mt-2 flex flex-wrap gap-2">
              <Categories categories={article.categories} />
              <Tags tags={article.tags} />
            </div>

            <div className="mt-4 flex items-center gap-x-3">
              <ProfileShow user={article.user} />
            </div>
          </div>
        </article>
      ))}
    </div>
  );
};
