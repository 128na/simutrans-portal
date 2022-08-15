<template>
  <div v-html="parsed" />
</template>
<script>
import MarkdownIt from 'markdown-it';
const md = new MarkdownIt();
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
    }
  }
};
</script>
