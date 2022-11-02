<template>
  <span class="q-mr-xs q-mb-xs">投稿：{{ publishedAt }}</span>
  <span class="q-mr-xs q-mb-xs">更新：{{ modifiedAt }}</span>
</template>
<script>
import { defineComponent, computed } from 'vue';
import { DateTime } from 'luxon';
import { DT_FORMAT } from 'src/const';

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
      publishedAt: computed(() => (props.article.published_at ? DateTime.fromISO(props.article.published_at).toFormat(DT_FORMAT) : '未投稿')),
      modifiedAt: computed(() => (props.article.modified_at ? DateTime.fromISO(props.article.modified_at).toFormat(DT_FORMAT) : '未投稿')),
    };
  },
});
</script>
