<template>
  <q-page>
    <q-item v-show="loading">
      <q-item-section>
        <loading-message />
      </q-item-section>
    </q-item>
    <q-item v-show="error">
      <q-item-section>
        <api-error-message :message="errorMessage" @retry="fetch($route)" />
      </q-item-section>
    </q-item>
    <q-item v-if="article">
      <front-article-show :article="article" />
    </q-item>
  </q-page>
</template>

<script>
import {
  defineComponent, ref, computed,
} from 'vue';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useArticleCacheStore } from 'src/store/articles';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import LoadingMessage from 'src/components/Common/LoadingMessage.vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';

export default defineComponent({
  name: 'FrontShow',
  components: {
    LoadingMessage,
    ApiErrorMessage,
    FrontArticleShow,
  },

  props: {
    cachedArticles: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const loading = ref(true);
    const error = ref(false);

    const route = useRoute();
    const { postShown } = useFrontApi();
    postShown(route.params.slug);

    const articleCache = useArticleCacheStore();

    const article = computed(() => articleCache.getCache(route.params.slug));

    const { errorMessage, errorHandlerStrict } = useErrorHandler(useRouter());
    const { fetchArticle } = useFrontApi();
    const fetch = async (currentRoute) => {
      const { setTitle } = useMeta();
      if (articleCache.hasCache(currentRoute.params.slug)) {
        loading.value = false;
        error.value = false;
        setTitle(article.value.title);
        return;
      }

      loading.value = true;
      error.value = false;
      try {
        const res = await fetchArticle(currentRoute.params.slug);
        if (res.status === 200) {
          articleCache.addCache(res.data.data);
          setTitle(res.data.data.title);
        }
      } catch (err) {
        error.value = true;
        errorHandlerStrict(err, '記事取得に失敗しました');
      } finally {
        loading.value = false;
      }
    };
    fetch(route);
    onBeforeRouteUpdate((to) => {
      fetch(to);
    });

    return {
      article,
      loading,
      error,
      errorMessage,
      fetch,
    };
  },
});
</script>