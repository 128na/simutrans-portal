<template>
  <q-page>
    <q-list>
      <q-item v-show="title">
        <q-item-section>
          <text-title>{{title}}</text-title>
        </q-item-section>
      </q-item>
      <q-item v-show="loading">
        <q-item-section>
          <loading-message />
        </q-item-section>
      </q-item>
      <q-item v-if=" pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="page => ({ query: { page } })" direction-links boundary-links />
      </q-item>
      <q-separator />
      <q-item v-show="error">
        <q-item-section>
          <api-error-message :message="errorMessage" @retry="fetchArticles($route)" />
        </q-item-section>
      </q-item>
      <front-article-list :articles="articles" />
      <q-item v-if=" pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="page => ({ query: { page } })" direction-links boundary-links />
      </q-item>
    </q-list>
  </q-page>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useArticleCacheStore } from 'src/store/articles';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import FrontArticleList from 'src/components/Front/FrontArticleList.vue';
import LoadingMessage from 'src/components/Common/LoadingMessage.vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';

export default defineComponent({
  name: 'FrontList',
  components: {
    FrontArticleList,
    LoadingMessage,
    ApiErrorMessage,
    TextTitle,
  },

  setup() {
    const articles = ref([]);
    const profile = ref(null);
    const pagination = ref(null);
    const loading = ref(true);
    const error = ref(false);

    const title = ref(null);
    const { setTitle } = useMeta();

    const articleCache = useArticleCacheStore();
    const handleResponse = (res) => {
      if (res.status === 200) {
        articles.value = res.data.data;
        pagination.value = res.data.meta;
        title.value = res.data.title;
        setTitle(res.data.title);
        profile.value = res.data?.profile || null;
        articleCache.addCaches(res.data.data);
      }
    };

    const router = useRouter();
    const api = useFrontApi();
    const { errorMessage, errorHandlerStrict } = useErrorHandler(router);
    const route = useRoute();
    const fetchArticles = async (currentRoute) => {
      loading.value = true;
      error.value = false;
      articles.value = [];

      try {
        const res = await (async () => {
          switch (currentRoute.name) {
            case 'categoryPak':
              return api.fetchCategoryPak(route.params.size, route.params.slug, route.query.page);
            case 'category':
              return api.fetchCategory(route.params.size, route.params.slug, route.query.page);
            case 'tag':
              return api.fetchTag(route.params.id, route.query.page);
            case 'user':
              return api.fetchUser(route.params.id, route.query.page);
            case 'announces':
              return api.fetchAnnounces(route.query.page);
            case 'pages':
              return api.fetchPages(route.query.page);
            case 'ranking':
              return api.fetchRanking(route.query.page);
            case 'search':
              return api.fetchSearch(route.query.word, route.query.page);
            default:
              throw new Error(`unknown route name "${route.params.name}" provided"`);
          }
        })();
        handleResponse(res);
      } catch (err) {
        error.value = true;
        errorHandlerStrict(err, '記事取得に失敗しました');
      } finally {
        loading.value = false;
      }
    };
    fetchArticles(route);
    onBeforeRouteUpdate((to) => {
      fetchArticles(to);
    });

    return {
      articles,
      profile,
      pagination,
      title,
      loading,
      error,
      errorMessage,
      fetchArticles,
    };
  },
});
</script>
