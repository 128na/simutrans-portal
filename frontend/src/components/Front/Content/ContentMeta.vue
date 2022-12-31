<template>
  <span class="q-mr-xs q-mb-xs">投稿：{{ publishedAt }}</span>
  <span class="q-mr-xs q-mb-xs">更新：{{ modifiedAt }}</span>
  <router-link v-if="canEdit" class="q-mr-xs q-mb-xs text-primary"
    :to="{ name: 'edit', params: { id: article.id } }">編集する</router-link>
</template>
<script>
import { defineComponent, computed } from 'vue';
import { DateTime } from 'luxon';
import { DT_FORMAT } from 'src/const';
import { useAuthStore } from 'src/store/auth';

export default defineComponent({
  name: 'ArticleMeta',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const auth = useAuthStore();
    const canEdit = computed(() => props.article.id && auth.isOwnedArticle(props.article));
    const publishedAt = computed(() => (props.article.published_at
      ? DateTime.fromISO(props.article.published_at).toFormat(DT_FORMAT)
      : '未投稿'
    ));
    const modifiedAt = computed(() => (props.article.modified_at
      ? DateTime.fromISO(props.article.modified_at).toFormat(DT_FORMAT)
      : '未投稿'
    ));

    return {
      canEdit,
      publishedAt,
      modifiedAt,
    };
  },
});
</script>
