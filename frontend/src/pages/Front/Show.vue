<template>
  <q-page>
    <q-item v-if="article">
      <front-article-show :article="article" />
    </q-item>
  </q-page>
</template>

<script>
import { defineComponent, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { useArticleCacheStore } from 'src/store/articleCache';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'FrontShow',
  components: {
    FrontArticleShow,
  },

  props: {
    cachedArticles: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const route = useRoute();
    const api = useFrontApi();
    api.postShown(route.params.slug);

    const articleCache = useArticleCacheStore();

    const article = computed(() => articleCache.getCache(route.params.slug));

    const handler = useApiHandler();
    const { setTitle } = useMeta();
    const fetch = async () => {
      if (articleCache.hasCache(route.params.slug)) {
        setTitle(article.value.title);
        return;
      }
      try {
        const res = await handler.handleWithLoading({ doRequest: () => api.fetchArticle(route.params.slug), failedMessage: '記事取得に失敗しました' });
        articleCache.addCache(res.data.data);
        setTitle(res.data.data.title);
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetch(); }, { deep: true, immediate: true });

    return {
      article,
    };
  },
});
</script>
