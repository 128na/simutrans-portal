<template>
  <q-page>
    <q-list>
      <q-item v-show="title">
        <q-item-section>
          <text-title>{{ title }}</text-title>
        </q-item-section>
      </q-item>
      <q-item v-if="profile">
        <user-profile :profile="profile" />
      </q-item>
      <q-item v-if="pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="page => ({ query: { page } })" direction-links boundary-links />
      </q-item>
      <q-separator />
      <front-article-list :articles="articles" />
      <q-item v-if="pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="page => ({ query: { page } })" direction-links boundary-links />
      </q-item>
    </q-list>
  </q-page>
</template>

<script>
import { defineComponent, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useArticleCacheStore } from 'src/store/articleCache';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import FrontArticleList from 'src/components/Front/FrontArticleList.vue';
import UserProfile from 'src/components/Common/UserProfile.vue';
import { useApiHandler } from 'src/composables/apiHandler';

const resolveApi = (api, route) => {
  switch (route.name) {
    case 'categoryPak':
      return api.fetchCategoryPak(route.params.size, route.params.slug, route.query.page);
    case 'category':
      return api.fetchCategory(route.params.type, route.params.slug, route.query.page);
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
};

export default defineComponent({
  name: 'FrontList',
  components: {
    FrontArticleList,
    TextTitle,
    UserProfile,
  },

  setup() {
    const articles = ref([]);
    const profile = ref(null);
    const pagination = ref(null);

    const title = ref(null);
    const { setTitle } = useMeta();

    const articleCache = useArticleCacheStore();
    const api = useFrontApi();
    const handler = useApiHandler();
    const route = useRoute();

    const fetchArticles = async () => {
      try {
        const res = await handler.handleWithLoading({ doRequest: () => resolveApi(api, route), failedMessage: '記事取得に失敗しました' });
        articles.value = res.data.data;
        pagination.value = res.data.meta;
        title.value = res.data.title;
        setTitle(res.data.title);
        profile.value = res.data?.profile || null;
        articleCache.addCaches(res.data.data);
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetchArticles(); }, { deep: true, immediate: true });

    return {
      articles,
      profile,
      pagination,
      title,
    };
  },
});
</script>
