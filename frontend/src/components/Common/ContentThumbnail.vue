<template>
  <img :src="thumbnailUrl" class="thumbnai" loading="lazy" />
</template>

<script>
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ContentThumbnail',
  components: {
  },
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const thumbnailUrl = (article) => {
      const attachmentId = parseInt(article.contents.thumbnail, 10);
      return article.attachments.find((a) => a.id === attachmentId)?.url
        || '/storage/default/image.png';
    };

    return {
      thumbnailUrl: computed(() => thumbnailUrl(props.article)),
    };
  },
});
</script>
<style lang="scss">
.thumbnai {
  max-width: 100%;
  max-height: 50vh;
}
</style>
