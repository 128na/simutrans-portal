<template v-if="article">
  <template-article :article="article" />
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
      default: () => window.article,
      required: false
    }
  }
};
</script>
