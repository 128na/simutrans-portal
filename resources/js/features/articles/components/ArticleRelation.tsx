import Link from "@/components/ui/Link";
import { TitleH4 } from "./TitleH4";

type Props = {
  title: string;
  articles: Article.Relational[];
  preview: boolean;
};

export const ArticleRelation = ({ title, articles, preview }: Props) => {
  if (articles.length === 0) {
    return null;
  }

  return (
    <div>
      <TitleH4>{title}</TitleH4>
      {articles.map((relatedArticle) => (
        <div>
          <Link
            href={preview ? "#" : `/articles/${relatedArticle.id}`}
            key={relatedArticle.id}
          >
            {relatedArticle.title}
          </Link>
        </div>
      ))}
    </div>
  );
};
