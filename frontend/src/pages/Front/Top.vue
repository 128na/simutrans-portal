<template>
  <q-page>
    <template v-for="(c, i) in contents" :key="i">
      <q-list>
        <q-item :to="c.to">
          <q-item-section>
            <q-item-label class="text-h2">{{c.label}}</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-show="c.loading">
          <q-item-section>
            <LoadingMessage />
          </q-item-section>
        </q-item>
        <q-item v-show="c.error">
          <q-item-section>
            <ApiErrorMessage message="記事取得に失敗しました" @retry="fetchContent($emit, c)" />
          </q-item-section>
        </q-item>
        <FrontArticleList :articles="c.articles" :listMode="listMode" />
        <q-separator />
      </q-list>
    </template>
  </q-page>
</template>

<script>
import { defineComponent, reactive } from 'vue';
import { api } from '../../boot/axios';
import { metaHandler } from '../../composables/metaHandler';
import FrontArticleList from '../../components/Front/FrontArticleList.vue';
import LoadingMessage from '../../components/Common/LoadingMessage.vue';
import ApiErrorMessage from '../../components/Common/ApiErrorMessage.vue';

const contents = reactive([
  {
    api: '/api/v3/front/category/pak/128-japan?simple',
    to: { name: 'category', params: { type: 'pak', slug: '128-japan' } },
    label: 'pak128Japanの新着アドオン',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/category/pak/128?simple',
    to: { name: 'category', params: { type: 'pak', slug: '128' } },
    label: 'pak128の新着アドオン',
    articles: [],
    error: false,
    loading: true,
  },
  {
    api: '/api/v3/front/category/pak/64?simple',
    to: { name: 'category', params: { type: 'pak', slug: '64' } },
    label: 'pak64の新着アドオン',
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

const fetchContent = async (emit, content) => {
  content.loading = true;
  content.error = false;
  content.articles = [];
  try {
    const res = await api.get(content.api);
    if (res.status === 200) {
      content.articles = JSON.parse(JSON.stringify(res.data.data)).splice(0, 6);
      emit('addCaches', res.data.data);
    }
  } catch (err) {
    content.error = true;
  } finally {
    content.loading = false;
  }
};
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
    contents.map((c) => fetchContent(emit, c));
    const { setTitle } = metaHandler();
    setTitle('top');

    return {
      contents,
      fetchContent,
    };
  },
});
</script>
