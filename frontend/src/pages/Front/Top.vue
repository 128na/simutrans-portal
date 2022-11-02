<template>
  <q-page>
    <template v-for="(c, i) in contents" :key="i">
      <q-list>
        <q-item :to="c.to">
          <q-item-section>
            <text-title>
              {{ c.label }}
            </text-title>
          </q-item-section>
        </q-item>
        <q-separator />
        <q-item v-show="c.loading">
          <q-item-section>
            <loading-message />
          </q-item-section>
        </q-item>
        <q-item v-show="c.error">
          <q-item-section>
            <api-error-message :message="errorMessage" @retry="fetchContent(c)" />
          </q-item-section>
        </q-item>
        <front-article-list :articles="c.articles" />
        <q-separator />
      </q-list>
    </template>
  </q-page>
</template>

<script>
import { defineComponent, reactive } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useArticleCacheStore } from 'src/store/articleCache';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import { useErrorHandler } from 'src/composables/errorHandler';
import FrontArticleList from 'src/components/Front/FrontArticleList.vue';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';

const contents = reactive([
  {
    api: '/api/v3/front/category/pak/128-japan?simple',
    to: { name: 'category', params: { type: 'pak', slug: '128-japan' } },
    label: 'pak128Japanの新着',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/category/pak/128?simple',
    to: { name: 'category', params: { type: 'pak', slug: '128' } },
    label: 'pak128の新着',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/category/pak/64?simple',
    to: { name: 'category', params: { type: 'pak', slug: '64' } },
    label: 'pak64の新着',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/ranking?simple',
    to: { name: 'ranking' },
    label: 'アクセスランキング',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/pages?simple',
    to: { name: 'pages' },
    label: '一般記事',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/announces?simple',
    to: { name: 'announces' },
    label: 'お知らせ',
    articles: [],
    error: false,
    loading: true,
  },
]);

export default defineComponent({
  name: 'FrontTop',
  components: {
    FrontArticleList,
    LoadingMessage,
    ApiErrorMessage,
    TextTitle,
  },

  setup() {
    const { errorHandler, errorMessage } = useErrorHandler();
    const articleCache = useArticleCacheStore();
    const { get } = useFrontApi();
    const fetchContent = async (content) => {
      content.loading = true;
      content.error = false;
      content.articles = [];
      try {
        const res = await get(content.api);
        if (res.status === 200) {
          content.articles = JSON.parse(JSON.stringify(res.data.data)).splice(0, 6);
          articleCache.addCaches(res.data.data);
        }
      } catch (err) {
        content.error = true;
        errorHandler(err, '記事取得に失敗しました');
      } finally {
        content.loading = false;
      }
    };
    contents.map((c) => fetchContent(c));
    const { setTitle } = useMeta();
    setTitle('top');

    return {
      contents,
      fetchContent,
      errorMessage,
    };
  },
});
</script>
