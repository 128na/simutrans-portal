<template v-if="article">
  <div>mounted {{ article.title }}</div>
</template>
<script>
export default {
  props: {
    is_preview: {
      type: Boolean,
      default: false
    },
    article: {
      type: Object,
      default: () => window.article
    }

  }
};
</script>
