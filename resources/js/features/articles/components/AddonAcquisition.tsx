import { Categories } from "./Categories";
import { displaySize } from "@/features/attachments/attachmentUtil";
import Button from "@/components/ui/Button";
import { AddToMyListButton } from "../../mylist/AddToMyList";

type Props = {
  article: Extract<
    Article.Show,
    { post_type: "addon-post" | "addon-introduction" }
  >;
  preview: boolean;
  isAuthenticated?: boolean;
};

/** 本文側の「ライセンス・利用条件」見出しの id。入手ボックスからリンクする */
export const ADDON_LICENSE_ANCHOR = "addon-license";

export const AddonAcquisition = ({
  article,
  preview,
  isAuthenticated = false,
}: Props) => {
  const paks = article.categories.filter((category) => category.type === "pak");
  const licenses = article.categories.filter(
    (category) => category.type === "license"
  );
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
  const licenseText = article.contents.license;
  const licenseLink = licenseText ? (
    <a href={`#${ADDON_LICENSE_ANCHOR}`} className="v2-link">
      利用条件を見る
    </a>
  ) : null;

  return (
    <section
      aria-label="アドオンの入手"
      className="grid gap-3 rounded-xl border border-c-border-strong bg-white p-4"
    >
      <h3 className="text-title font-semibold">アドオンの入手</h3>
      <div className="grid gap-2">
        {preview ? (
          <Button disabled className="w-full justify-center">
            {label}
          </Button>
        ) : href ? (
          <a
            href={href}
            className="inline-flex w-full justify-center rounded-lg bg-c-primary px-3 py-2.5 text-center text-sm font-semibold text-white hover:bg-c-link focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-c-primary"
          >
            {label}
          </a>
        ) : (
          <p className="text-note text-c-sub">入手先は設定されていません。</p>
        )}
        {file && (
          <p className="break-all text-center text-note text-c-sub">
            {file.original_name} ・ {displaySize(file.size)}
          </p>
        )}
        {!local && href && !linkType && (
          <p className="text-note text-c-sub">
            外部サイトで配布ページまたはファイルが開きます。
          </p>
        )}
        {isAuthenticated && !preview && article.id && (
          <AddToMyListButton
            articleId={article.id}
            className="w-full justify-center"
          />
        )}
      </div>
      <dl className="grid grid-cols-[auto_minmax(0,1fr)] items-baseline gap-x-4 gap-y-2 border-t border-c-border pt-3 text-note">
        <dt className="text-c-sub">対応pak</dt>
        <dd className="flex flex-wrap gap-1">
          {paks.length > 0 ? (
            <Categories categories={paks} preview={preview} />
          ) : (
            "未指定"
          )}
        </dd>
        <dt className="text-c-sub">ライセンス</dt>
        <dd className="flex flex-wrap items-baseline gap-x-2 gap-y-1">
          {licenses.length > 0 ? (
            <Categories categories={licenses} preview={preview} />
          ) : (
            !licenseText && "未指定"
          )}
          {licenseLink}
        </dd>
        <dt className="text-c-sub">作者</dt>
        <dd className="break-all">
          {article.contents.author || article.user.name}
        </dd>
        {article.post_type === "addon-introduction" && (
          <>
            <dt className="text-c-sub">掲載許可</dt>
            <dd>{article.contents.agreement ? "取得済み" : "未取得"}</dd>
          </>
        )}
      </dl>
    </section>
  );
};
