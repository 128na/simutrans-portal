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
    const articleCache = useArticleCacheStore();
    const article = computed(() => articleCache.getCache(route.params.slug));

    const handler = useApiHandler();
    const { setTitle } = useMeta();
    const fetch = async () => {
      if (route.name !== 'show') {
        return;
      }
      if (articleCache.hasCache(route.params.slug)) {
        setTitle(article.value.title);
        api.postShown(article.value.id);
        return;
      }
      try {
        await handler.handleWithLoading({
          doRequest: () => api.fetchArticle(route.params.id, route.params.slug),
          done: (res) => {
            articleCache.addCache(res.data.data);
            setTitle(res.data.data.title);
            api.postShown(res.data.data.id);
          },
          failedMessage: '記事取得に失敗しました',
        });
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
