import MarkdownIt from 'markdown-it';
import sanitizeHtml from 'sanitize-html';

// https://github.com/markdown-it/markdown-it
const markdown = new MarkdownIt({
  html: true,
  breaks: true,
  linkify: true,
  typographer: true,
});

export const render = (content) => markdown.render(content);

// https://www.npmjs.com/package/sanitize-html
// HTMLPurifierと同等設定
export const sanitize = (content) => sanitizeHtml(content, {
  allowedTags: [
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
    'hr',
    'pre', 'code',
    'blockquote',
    'table', 'tr', 'td', 'th', 'thead', 'tbody',
    'strong', 'em', 'b', 'i', 'u', 's', 'span',
    'a', 'p', 'br',
    'ul', 'ol', 'li',
    'img',
  ],
  allowedAttributes: {
    a: ['href', 'name', 'target'],
    img: ['src', 'srcset', 'alt', 'title', 'width', 'height', 'loading'],
  },
});

// 全消し
export const sanitizeAll = (content) => sanitizeHtml(content, {
  allowedTags: [],
  allowedAttributes: {
  },
});
