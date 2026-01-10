import { Categories } from "./Categories";
import { Tags } from "./Tags";
import { Thumbnail } from "@/components/ui/Thumbnail";
import { TitleH4 } from "./TitleH4";
import { ArticleRelation } from "./ArticleRelation";
import { ProfileShow } from "../../user/ProfileShow";
import { AddToMyListButton } from "../../mylist/AddToMyList";
import { match } from "ts-pattern";
import { JSX } from "react";
import { Page } from "./postType/Page";
import { Markdown } from "./postType/Markdown";
import { AddonPost } from "./postType/AddonPost";
import { AddonIntroduction } from "./postType/AddonIntroduction";

type Props = {
  article: Article.Show;
  preview?: boolean;
  isAuthenticated?: boolean;
};
export const ArticleBase = ({
  article,
  preview = false,
  isAuthenticated = false,
}: Props) => {
  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between gap-4 mb-2">
        <div className="text-sm flex flex-wrap gap-2">
          <Categories categories={article.categories} preview={preview} />
          <Tags tags={article.tags} preview={preview} />
        </div>
        {isAuthenticated && !preview && article.id && (
          <AddToMyListButton articleId={article.id} />
        )}
      </div>

      <Thumbnail
        attachmentId={article.contents.thumbnail}
        attachments={article.attachments}
        openFullSize={preview ? false : true}
      />

      {match<ArticlePostType>(article.post_type)
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

      <div>
        <TitleH4>投稿者</TitleH4>
        <ProfileShow user={article.user} preview={preview} />
      </div>
    </div>
  );
};
