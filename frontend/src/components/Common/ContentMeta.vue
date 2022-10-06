<template>
  <span class="q-mr-xs q-mb-xs">投稿：{{ publishedAt }}</span>
  <span class="q-mr-xs q-mb-xs">更新：{{ modifiedAt }}</span>
</template>
<script>
import { defineComponent, computed } from 'vue';
import { DateTime } from 'luxon';

export default defineComponent({
  name: 'ArticleMeta',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return {
      publishedAt: computed(() => DateTime.fromISO(props.article.published_at).toISODate()),
      modifiedAt: computed(() => DateTime.fromISO(props.article.modified_at || props.article.published_at).toISODate()),
    };
  },
});
</script>
