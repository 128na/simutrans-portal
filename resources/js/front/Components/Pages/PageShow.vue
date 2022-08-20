<template v-if="article">
  <template-article :article="article" :attachments="attachments" />
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
    },
    attachments: {
      type: Array,
      default: () => window.attachments
    }
  }
};
</script>
