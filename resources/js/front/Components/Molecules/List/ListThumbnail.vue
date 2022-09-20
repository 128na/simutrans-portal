<template>
  <img :src="thumbnail_url" loading="lazy" />
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  computed: {
    thumbnail_url() {
      return this.article.attachments.find(a => a.id == this.article.contents.thumbnail)?.url ||
        '/storage/default/image.png';
    }
  }
};
</script>
