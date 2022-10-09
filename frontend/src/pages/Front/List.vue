<template>
  <q-page>
    <q-list>
      <q-item v-show="title">
        <q-item-section>
          <text-title>{{title}}</text-title>
        </q-item-section>
      </q-item>
      <q-separator />
      <q-item v-show="loading">
        <q-item-section>
          <loading-message />
        </q-item-section>
      </q-item>
      <q-item v-show="error">
        <q-item-section>
          <api-error-message :message="errorMessage" @retry="fetchArticles($route)" />
        </q-item-section>
      </q-item>
      <front-article-list :articles="articles" :listMode="listMode" />
    </q-list>
  </q-page>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { api } from '../../boot/axios';
import { metaHandler } from '../../composables/metaHandler';
import FrontArticleList from '../../components/Front/FrontArticleList.vue';
import LoadingMessage from '../../components/Common/LoadingMessage.vue';
import ApiErrorMessage from '../../components/Common/ApiErrorMessage.vue';

const fetchUser = (route) => api.get(`/api/v3/front/user/${route.params.id}?page=${route.query.page || 1}`);
const fetchCategoryPak = (route) => api.get(`/api/v3/front/category/pak/${route.params.size}/${route.params.slug}?page=${route.query.page || 1}`);
const fetchCategory = (route) => api.get(`/api/v3/front/category/${route.params.type}/${route.params.slug}?page=${route.query.page || 1}`);
const fetchTag = (route) => api.get(`/api/v3/front/tag/${route.params.id}?page=${route.query.page || 1}`);
const fetchAnnounces = (route) => api.get(`/api/v3/front/announces?page=${route.query.page || 1}`);
const fetchPages = (route) => api.get(`/api/v3/front/pages?page=${route.query.page || 1}`);
const fetchRanking = (route) => api.get(`/api/v3/front/ranking?page=${route.query.page || 1}`);
const fetchSearch = (route) => api.get(`/api/v3/front/search?word=${route.query.word}&page=${route.query.page || 1}`);

export default defineComponent({
  name: 'FrontTop',
  components: {
    FrontArticleList,
    LoadingMessage,
    ApiErrorMessage,
    TextTitle,
  },

  props: {
    listMode: {
      type: String,
      default: 'list',
    },
  },

  setup(props, { emit }) {
    const articles = ref([]);
    const profile = ref(null);
    const pagination = ref(null);
    const loading = ref(true);
    const error = ref(false);

    const title = ref(null);
    const { setTitle } = metaHandler();

    const handleResponse = (res) => {
      if (res.status === 200) {
        articles.value = res.data.data;
        pagination.value = res.data.meta;
        title.value = res.data.title;
        setTitle(res.data.title);
        profile.value = res.data?.profile || null;
        emit('addCaches', res.data.data);
      }
    };

    const { errorMessage, errorHandlerStrict } = useErrorHandler(useRouter());
    const route = useRoute();
    const fetchArticles = async (currentRoute) => {
      loading.value = true;
      error.value = false;
      articles.value = [];

      try {
        const res = await (async () => {
          switch (currentRoute.name) {
            case 'categoryPak':
              return fetchCategoryPak(currentRoute);
            case 'category':
              return fetchCategory(currentRoute);
            case 'tag':
              return fetchTag(currentRoute);
            case 'user':
              return fetchUser(currentRoute);
            case 'announces':
              return fetchAnnounces(currentRoute);
            case 'pages':
              return fetchPages(currentRoute);
            case 'ranking':
              return fetchRanking(currentRoute);
            case 'search':
              return fetchSearch(currentRoute);
            default:
              throw new Error(`unknown route name "${currentRoute.name}" provided"`);
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
