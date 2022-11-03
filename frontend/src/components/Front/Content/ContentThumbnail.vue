<template>
  <img :src="thumbnailUrl" class="thumbnai" loading="lazy" />
</template>

<script>
import { DEFAULT_THUMBNAIL } from 'src/const';
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
    const thumbnailUrl = computed(() => {
      if (props.article.contents.thumbnail) {
        const attachmentId = parseInt(props.article.contents.thumbnail, 10);
        return props.article.attachments.find((a) => a.id === attachmentId)?.url
          || DEFAULT_THUMBNAIL;
      }
      return DEFAULT_THUMBNAIL;
    });

    return {
      thumbnailUrl,
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
