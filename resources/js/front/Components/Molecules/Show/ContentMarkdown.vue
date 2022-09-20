<template>
  <div>
    <div v-html="sanitized" />
  </div>
</template>
<script>
import { render, sanitize } from '../../../plugins/markdownParser';

export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  computed: {
    parsed() {
      return render(this.article.contents.markdown);
    },
    sanitized() {
      return sanitize(this.parsed);
    }
  }
};
</script>
