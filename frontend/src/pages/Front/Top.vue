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
        <q-item v-show="c.handler.loading">
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
import { defineComponent, reactive } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useArticleCacheStore } from 'src/store/articleCache';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import FrontArticleList from 'src/components/Front/FrontArticleList.vue';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'FrontTop',
  components: {
    FrontArticleList,
    TextTitle,
    LoadingMessage,
  },

  setup() {
    const contents = reactive([
      {
        wait: 0,
        api: '/api/v3/front/category/pak/128-japan?simple',
        to: { name: 'category', params: { type: 'pak', slug: '128-japan' } },
        label: 'pak128Japanの新着',
        articles: null,
        handler: useApiHandler(),
      },
      {
        wait: 100,
        api: '/api/v3/front/category/pak/128?simple',
        to: { name: 'category', params: { type: 'pak', slug: '128' } },
        label: 'pak128の新着',
        articles: null,
        handler: useApiHandler(),
      },
      {
        wait: 200,
        api: '/api/v3/front/category/pak/64?simple',
        to: { name: 'category', params: { type: 'pak', slug: '64' } },
        label: 'pak64の新着',
        articles: null,
        handler: useApiHandler(),
      },
      {
        wait: 300,
        api: '/api/v3/front/ranking?simple',
        to: { name: 'ranking' },
        label: 'アクセスランキング',
        articles: null,
        handler: useApiHandler(),
      },
      {
        wait: 1000,
        api: '/api/v3/front/pages?simple',
        to: { name: 'pages' },
        label: '一般記事',
        articles: null,
        handler: useApiHandler(),
      },
      {
        wait: 1000,
        api: '/api/v3/front/announces?simple',
        to: { name: 'announces' },
        label: 'お知らせ',
        articles: null,
        handler: useApiHandler(),
      },
    ]);
    const articleCache = useArticleCacheStore();
    const { get } = useFrontApi();
    contents.forEach((content) => {
      content.articles = null;
      setTimeout(async () => {
        try {
          await content.handler.handle({
            doRequest: () => get(content.api),
            done: (res) => {
              content.articles = JSON.parse(JSON.stringify(res.data.data));
              articleCache.addCaches(res.data.data);
            },
            failedMessage: `${content.label}一覧の取得に失敗しました`,
          });
        } catch {
          // do nothing.
        }
      }, content.wait);
    });
    const { setTitle } = useMeta();
    setTitle('top');

    return {
      contents,
    };
  },
});
</script>
