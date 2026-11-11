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
import { formatArticleDate } from "../utils/articleUtil";

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
  const updated =
    article.modified_at && article.modified_at !== article.published_at
      ? article.modified_at
      : null;

  return (
    <div className="grid gap-6">
      {/* タイトル直下：カテゴリ・タグ・公開日／更新日 */}
      <div className="flex flex-wrap items-center gap-x-3 gap-y-2">
        <div className="flex flex-wrap gap-1.5">
          <Categories categories={article.categories} preview={preview} />
          <Tags tags={article.tags} preview={preview} />
        </div>
        {(updated || article.published_at) && (
          <p className="flex flex-wrap gap-x-3 text-note text-c-sub">
            {updated && <span>{formatArticleDate(updated)} 更新</span>}
            {article.published_at && (
              <span>{formatArticleDate(article.published_at)} 公開</span>
            )}
          </p>
        )}
        {!isAddon && isAuthenticated && !preview && article.id && (
          <AddToMyListButton articleId={article.id} />
        )}
      </div>

      <div
        className={
          sidebar
            ? "grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] xl:grid-cols-[minmax(0,1fr)_20rem]"
            : "grid gap-6"
        }
      >
        <div className="grid min-w-0 gap-6">
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
            <AddonAcquisition
              article={article}
              preview={preview}
              isAuthenticated={isAuthenticated}
            />
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
    </div>
  );
};
