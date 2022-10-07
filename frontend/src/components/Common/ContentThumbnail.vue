<template>
  <img :src="thumbnailUrl" class="thumbnai" />
</template>

<script>
import { defineComponent, computed } from 'vue';

const thumbnailUrl = (article) => {
  const attachmentId = parseInt(article.contents.thumbnail, 10);
  return article.attachments.find((a) => a.id === attachmentId)?.url
    || `${process.env.BACKEND_URL}/storage/default/image.png`;
};

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
    return {
      thumbnailUrl: computed(() => thumbnailUrl(props.article)),
    };
  },
});
</script>
<style lang="scss">
.thumbnai {
  max-width: 100%;
}
</style>
