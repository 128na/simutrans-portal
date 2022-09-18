<template>
  <img v-if="thumbnail_url" :src="thumbnail_url" class="img-fluid thumbnail mb-4 shadow-sm" />
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    },
    attachments: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    thumbnail_url() {
      return this.attachments.find(a => a.id == this.article.contents.thumbnail)?.url ||
        '/storage/default/image.png';
    }
  }
};
</script>
