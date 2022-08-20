<template>
  <div>
    <div v-html="sanitized" />
  </div>
</template>
<script>
import MarkdownIt from 'markdown-it';
import sanitizeHtml from 'sanitize-html';

// https://github.com/markdown-it/markdown-it
const md = new MarkdownIt({
  html: true,
  breaks: true,
  linkify: true,
  typographer: true
});

export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  computed: {
    parsed() {
      return md.render(this.article.contents.markdown);
    },
    sanitized() {
      // https://www.npmjs.com/package/sanitize-html
      // HTMLPurifierと同等設定
      return sanitizeHtml(this.parsed, {
        allowedTags: [
          'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
          'hr',
          'pre', 'code',
          'blockquote',
          'table', 'tr', 'td', 'th', 'thead', 'tbody',
          'strong', 'em', 'b', 'i', 'u', 's', 'span',
          'a', 'p', 'br',
          'ul', 'ol', 'li',
          'img'
        ],
        allowedAttributes: {
          a: ['href', 'name', 'target'],
          img: ['src', 'srcset', 'alt', 'title', 'width', 'height', 'loading']
        }
      });
    }
  }
};
</script>
