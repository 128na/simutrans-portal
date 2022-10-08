<template>
  <q-page>
    <q-list>
      <q-item v-show="title">
        <q-item-section>
          <q-item-label class="text-h2">{{title}}</q-item-label>
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
          <api-error-message message="記事取得に失敗しました" @retry="fetchArticles" />
        </q-item-section>
      </q-item>
      <front-article-list :articles="articles" :listMode="listMode" />
    </q-list>
  </q-page>
</template>

<script>
import { defineComponent, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
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
    const route = useRoute();
    const fetchArticles = async () => {
      loading.value = true;
      error.value = false;
      articles.value = [];

      try {
        const res = await (async () => {
          switch (route.name) {
            case 'categoryPak':
              return fetchCategoryPak(route);
            case 'category':
              return fetchCategory(route);
            case 'tag':
              return fetchTag(route);
            case 'user':
              return fetchUser(route);
            case 'announces':
              return fetchAnnounces(route);
            case 'pages':
              return fetchPages(route);
            case 'ranking':
              return fetchRanking(route);
            case 'search':
              return fetchSearch(route);
            default:
              throw new Error(`unknown route name "${route.name}" provided"`);
          }
        })();
        handleResponse(res);
      } catch (err) {
        error.value = true;
        // console.warn(err.response);
      } finally {
        loading.value = false;
      }
    };
    fetchArticles();

    watch(() => route.params, () => fetchArticles());

    return {
      articles,
      profile,
      pagination,
      title,
      loading,
      error,
      fetchArticles,
    };
  },
});
</script>
