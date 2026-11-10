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
  const sidebar = isAddon && !preview;
  const categories = isAddon
    ? article.categories.filter((category) => category.type !== "pak")
    : article.categories;
  const metadata = (
    <div className="flex flex-wrap items-center gap-3">
      <div className="flex flex-wrap gap-2 text-sm">
        <Categories categories={categories} preview={preview} />
        <Tags tags={article.tags} preview={preview} />
      </div>
      {isAuthenticated && !preview && article.id && (
        <AddToMyListButton articleId={article.id} />
      )}
    </div>
  );

  return (
    <div
      className={
        sidebar
          ? "grid gap-6 lg:grid-cols-[minmax(0,1fr)_14rem] xl:grid-cols-[minmax(0,1fr)_16rem]"
          : "grid gap-6"
      }
    >
      <div className="grid min-w-0 gap-6">
        {!isAddon && metadata}
        <Thumbnail
          attachmentId={article.contents.thumbnail}
          attachments={article.attachments}
          openFullSize={!preview}
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
      </div>

      {isAddon && (
        <aside
          aria-label="入手先と記事情報"
          className={
            sidebar
              ? "grid min-w-0 gap-4 lg:col-start-2 lg:row-start-1 lg:row-span-2 lg:sticky lg:top-6 lg:self-start lg:max-h-[calc(100dvh-3rem)] lg:overflow-y-auto"
              : "grid min-w-0 gap-4"
          }
        >
          <AddonAcquisition article={article} preview={preview} />
          {metadata}
        </aside>
      )}

      <div className="grid min-w-0 gap-6">
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
    </div>
  );
};
