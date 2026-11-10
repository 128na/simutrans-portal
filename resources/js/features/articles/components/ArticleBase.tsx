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
import { AddonAcquisition } from "./AddonAcquisition";

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
  const isAddon =
    article.post_type === "addon-post" ||
    article.post_type === "addon-introduction";
  const categories = isAddon
    ? article.categories.filter((category) => category.type !== "pak")
    : article.categories;

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between gap-4 mb-2">
        <div className="text-sm flex flex-wrap gap-2">
          <Categories categories={categories} preview={preview} />
          <Tags tags={article.tags} preview={preview} />
        </div>
        {isAuthenticated && !preview && article.id && (
          <AddToMyListButton articleId={article.id} />
        )}
      </div>

      {isAddon && <AddonAcquisition article={article} preview={preview} />}

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
        .with("addon-post", () => <AddonPost article={article} />)
        .with("addon-introduction", () => (
          <AddonIntroduction article={article} />
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
