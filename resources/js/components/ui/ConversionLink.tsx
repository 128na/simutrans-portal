type Props = {
  article: Article.List;
};
export default function ConversionLink({ article }: Props) {
  if (article.download_url) {
    return (
      <a
        className="v2-link"
        href={article.download_url}
        target="_blank"
        rel="noopener noreferrer"
      >
        ダウンロードする
      </a>
    );
  }

  if (article.addon_page_url) {
    return (
      <a href={article.addon_page_url} className="v2-link-external">
        掲載ページへ
      </a>
    );
  }

  return null;
}
