import { Categories } from "./Categories";
import { formatArticleDate } from "../utils/articleUtil";
import { displaySize } from "@/features/attachments/attachmentUtil";
import Button from "@/components/ui/Button";

type Props = {
  article: Extract<
    Article.Show,
    { post_type: "addon-post" | "addon-introduction" }
  >;
  preview: boolean;
};

export const AddonAcquisition = ({ article, preview }: Props) => {
  const paks = article.categories.filter((category) => category.type === "pak");
  const local = article.post_type === "addon-post";
  const fileId = local ? article.contents.file : null;
  const file = article.attachments.find(
    (attachment) => attachment.id === fileId
  );
  const linkType = local ? null : article.contents.link_type;
  const label = local
    ? "アドオンをダウンロード"
    : linkType === "page"
      ? "外部配布ページを開く"
      : linkType === "file"
        ? "外部ファイルを開く"
        : "外部の配布先を開く";
  const href = local
    ? file
      ? article.download_url
      : undefined
    : article.contents.link
      ? article.addon_page_url
      : undefined;

  return (
    <section
      aria-label="アドオンの入手"
      className="space-y-3 rounded-lg border border-c-primary/25 bg-c-primary/8 p-4"
    >
      <h3 className="font-semibold">アドオンの入手</h3>
      <dl className="flex flex-wrap gap-x-5 gap-y-2 text-sm">
        <div className="flex items-center flex-wrap gap-2">
          <dt className="text-c-sub">対応pak</dt>
          <dd className="flex flex-wrap gap-1">
            {paks.length > 0 ? (
              <Categories categories={paks} preview={preview} />
            ) : (
              "未指定"
            )}
          </dd>
        </div>
        <div className="flex gap-2">
          <dt className="text-c-sub">作者</dt>
          <dd>{article.contents.author || article.user.name}</dd>
        </div>
        <div className="flex gap-2">
          <dt className="text-c-sub">最終更新</dt>
          <dd>
            {formatArticleDate(article.modified_at ?? article.published_at)}
          </dd>
        </div>
      </dl>
      <div className="flex flex-wrap items-center gap-3">
        {preview ? (
          <Button disabled>{label}</Button>
        ) : href ? (
          <a
            href={href}
            className="inline-flex w-full justify-center rounded-lg bg-[color-mix(in_srgb,var(--color-c-primary)_86%,black)] px-3 py-2.5 text-center text-sm font-semibold text-white hover:bg-[color-mix(in_srgb,var(--color-c-primary)_75%,black)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-c-primary"
          >
            {label}
          </a>
        ) : (
          <p className="text-sm text-c-sub">入手先は設定されていません。</p>
        )}
        {file && (
          <span className="break-all text-sm text-c-sub">
            {file.original_name} ({displaySize(file.size)})
          </span>
        )}
        {!local && href && !linkType && (
          <span className="text-sm text-c-sub">
            外部サイトで配布ページまたはファイルが開きます。
          </span>
        )}
      </div>
    </section>
  );
};
