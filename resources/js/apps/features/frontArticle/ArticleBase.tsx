import { Categories } from "./Categories";
import { Tags } from "./Tags";
import { Thumbnail } from "@/apps/components/ui/Thumbnail";
import { TitleH4 } from "./TitleH4";
import { ArticleRelation } from "./ArticleRelation";
import { ProfileShow } from "./ProfileShow";
import { match } from "ts-pattern";
import { JSX } from "react";
import { Page } from "./postType/Page";
import { Markdown } from "./postType/Markdown";
import { AddonPost } from "./postType/AddonPost";
import { AddonIntroduction } from "./postType/AddonIntroduction";

type Props = {
  article: ArticleShow.Article;
  preview?: boolean;
};
export const ArticleBase = ({ article, preview = false }: Props) => {
  return (
    <>
      <div className="text-sm flex flex-wrap gap-2">
        <Categories categories={article.categories} preview={preview} />
        <Tags tags={article.tags} preview={preview} />
      </div>
      <Thumbnail
        attachmentId={article.contents.thumbnail}
        attachments={article.attachments}
      />
      {match<Article.PostType>(article.post_type)
        .returnType<JSX.Element>()
        .with("page", () => <Page article={article} preview={preview} />)
        .with("markdown", () => (
          <Markdown article={article} preview={preview} />
        ))
        .with("addon-post", () => (
          <AddonPost article={article} preview={preview} />
        ))
        .with("addon-introduction", () => (
          <AddonIntroduction article={article} preview={preview} />
        ))
        .exhaustive()}

      <ArticleRelation
        title="関連記事"
        articles={article.articles}
        preview={preview}
      />
      <ArticleRelation
        title="関連付けられた記事"
        articles={article.relatedArticles}
        preview={preview}
      />
      <TitleH4>投稿者</TitleH4>
      <ProfileShow user={article.user} preview={preview} />
    </>
  );
};
