<template>
  <q-page>
    <template v-for="(c, i) in contents" :key="i">
      <q-list>
        <q-item :to="c.to">
          <q-item-section>
            <text-title>
              <q-icon color="primary" name="chevron_right" size="md" />
              {{ c.label }}
            </text-title>
          </q-item-section>
        </q-item>
        <q-separator />
        <q-item v-show="handler.loading.value">
          <q-item-section>
            <loading-message />
          </q-item-section>
        </q-item>
        <template v-if="c.articles">
          <front-article-list :articles="c.articles" />
        </template>
        <q-separator />
      </q-list>
    </template>
  </q-page>
</template>

<script>
import { defineComponent, watch, reactive } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useArticleCacheStore } from 'src/store/articleCache';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import FrontArticleList from 'src/components/Front/FrontArticleList.vue';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import { useApiHandler } from 'src/composables/apiHandler';
import { useRoute } from 'vue-router';

export default defineComponent({
  name: 'FrontTop',
  components: {
    FrontArticleList,
    TextTitle,
    LoadingMessage,
  },

  setup() {
    const handler = useApiHandler();
    const { setTitle } = useMeta();
    setTitle('top');

    const articleCache = useArticleCacheStore();
    const contents = reactive({
      pak128japan: {
        to: { name: 'category', params: { type: 'pak', slug: '128-japan' } },
        label: 'pak128Japanの新着',
        articles: null,
      },
      pak128: {
        to: { name: 'category', params: { type: 'pak', slug: '128' } },
        label: 'pak128の新着',
        articles: null,
      },
      pak64: {
        to: { name: 'category', params: { type: 'pak', slug: '64' } },
        label: 'pak64の新着',
        articles: null,
      },
      rankings: {
        to: { name: 'ranking' },
        label: 'アクセスランキング',
        articles: null,
      },
      pages: {
        to: { name: 'pages' },
        label: '一般記事',
        articles: null,
      },
      announces: {
        to: { name: 'announces' },
        label: 'お知らせ',
        articles: null,
      },
    });
    const route = useRoute();
    const api = useFrontApi();
    const fetch = async () => {
      if (route.name !== 'top') {
        return;
      }
      try {
        await handler.handle({
          doRequest: () => api.fetchTop(),
          done: (res) => {
            Object.keys(res.data).forEach((key) => {
              if (contents[key] === undefined || contents[key].articles === undefined) {
                throw new Error(`missing key:${key}`);
              }
              contents[key].articles = res.data[key];
              articleCache.addCache(res.data[key]);
            });
          },
          failedMessage: '記事取得に失敗しました',
        });
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetch(); }, { deep: true, immediate: true });

    return {
      contents,
      handler,
    };
  },
});
</script>
