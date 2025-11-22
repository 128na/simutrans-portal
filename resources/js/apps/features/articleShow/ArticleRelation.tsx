import Link from "@/apps/components/ui/Link";
import { TitleH4 } from "./TitleH4";

type Props = {
  title: string;
  articles: ArticleShow.RelationaArticle[];
};

export const ArticleRelation = ({ title, articles }: Props) => {
  if (articles.length === 0) {
    return null;
  }

  return (
    <>
      <TitleH4>{title}</TitleH4>
      {articles.map((relatedArticle) => (
        <div>
          <Link href={`/articles/${relatedArticle.id}`} key={relatedArticle.id}>
            {relatedArticle.title}
          </Link>
        </div>
      ))}
    </>
  );
};
