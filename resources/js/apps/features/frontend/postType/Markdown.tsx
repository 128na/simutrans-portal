import MarkdownIt from "markdown-it";
import sanitizeHtml from "sanitize-html";

type Props = {
  article: Article.Show;
  preview: boolean;
};
const markdown = new MarkdownIt({
  html: true,
  breaks: true,
  linkify: true,
  typographer: true,
});
const render = (content: string) => markdown.render(content);
// https://www.npmjs.com/package/sanitize-html
// HTMLPurifierと同等設定
const sanitize = (content: string) =>
  sanitizeHtml(content, {
    allowedTags: [
      ...["h1", "h2", "h3", "h4", "h5", "h6"],
      ...["hr", "pre", "code", "blockquote"],
      ...["table", "tr", "td", "th", "thead", "tbody"],
      ...["strong", "em", "b", "i", "u", "s"],
      ...["span", "a", "p", "br", "ul", "ol", "li", "img"],
    ],
    allowedAttributes: {
      a: ["href", "name", "target"],
      img: ["src", "srcset", "alt", "title", "width", "height", "loading"],
    },
  });

export const Markdown = ({ article }: Props) => {
  const contents = article.contents as ArticleContent.Markdown;

  return (
    <div className="markdown-body break-all">
      <div
        dangerouslySetInnerHTML={{
          __html: sanitize(render(contents.markdown || "(本文未入力)")),
        }}
      />
    </div>
  );
};
