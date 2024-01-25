<template>
  <q-page>
    <q-list>
      <q-item v-show="title">
        <q-item-section>
          <text-title>{{ title }}</text-title>
        </q-item-section>
      </q-item>
      <q-item v-if="description">
        <description-handler :description="description" />
      </q-item>
      <q-item v-if="pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="handlePagination" direction-links boundary-links />
      </q-item>
      <q-separator />
      <front-article-list :articles="articles" />
      <q-item v-if="pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="handlePagination" direction-links boundary-links />
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
import DescriptionHandler from 'src/components/Front/Description/DescriptionHandler.vue';
import { useApiHandler } from 'src/composables/apiHandler';
import { useOrderModeStore } from 'src/store/listMode';

const resolveApi = (api, order, route) => {
  switch (route.name) {
    case 'categoryPak':
      return api.fetchCategoryPak(route.params.size, route.params.slug, order.currentMode, route.query.page);
    case 'category':
      return api.fetchCategory(route.params.type, route.params.slug, order.currentMode, route.query.page);
    case 'tag':
      return api.fetchTag(route.params.id, order.currentMode, route.query.page);
    case 'user':
      return api.fetchUser(route.params.idOrNickname, order.currentMode, route.query.page);
    case 'announces':
      return api.fetchAnnounces(order.currentMode, route.query.page);
    case 'pages':
      return api.fetchPages(order.currentMode, route.query.page);
    case 'ranking':
      return api.fetchRanking(route.query.page);
    case 'search':
      return api.fetchSearch(route.query.word, order.currentMode, route.query.page);
    default:
      throw new Error(`unknown route name "${route.params.name}" provided"`);
  }
};

export default defineComponent({
  name: 'FrontList',
  components: {
    FrontArticleList,
    TextTitle,
    DescriptionHandler,
  },

  setup() {
    const articles = ref([]);
    const description = ref(null);
    const pagination = ref(null);

    const title = ref(null);
    const { setTitle } = useMeta();

    const articleCache = useArticleCacheStore();
    const api = useFrontApi();
    const handler = useApiHandler();
    const route = useRoute();
    const order = useOrderModeStore();

    const fetchArticles = async () => {
      try {
        await handler.handleWithLoading({
          doRequest: () => resolveApi(api, order, route),
          done: (res) => {
            articles.value = res.data.data;
            pagination.value = res.data.meta;
            title.value = res.data.description.title;
            setTitle(title.value);
            description.value = res.data?.description || null;
            articleCache.addCaches(res.data.data);
          },
          failedMessage: '記事取得に失敗しました',
        });
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetchArticles(); }, { deep: true, immediate: true });
    watch(order, () => { fetchArticles(); });

    const handlePagination = (page) => ({
      query: { ...route.query, page },
    });

    return {
      articles,
      description,
      pagination,
      title,
      handlePagination,
    };
  },
});
</script>
