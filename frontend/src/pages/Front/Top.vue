<template>
  <q-page>
    <q-list v-if="pr">
      <q-item :to="{ name: 'show', params: { idOrNickname: pr.user.nickname || pr.user.id, slug: pr.slug } }">
        <q-item-section>
          [PR] {{ pr.title }}
        </q-item-section>
      </q-item>
    </q-list>
    <q-separator />
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
import {
  defineComponent, watch, reactive, ref,
} from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useArticleCacheStore } from 'src/store/articleCache';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import FrontArticleList from 'src/components/Front/FrontArticleList.vue';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import { useApiHandler } from 'src/composables/apiHandler';
import { useRoute } from 'vue-router';
import { useOrderModeStore } from 'src/store/listMode';

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
    const pr = ref(null);
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
    const order = useOrderModeStore();
    const fetch = async () => {
      if (route.name !== 'top') {
        return;
      }
      try {
        await handler.handle({
          doRequest: () => api.fetchTop(order.currentMode),
          done: (res) => {
            pr.value = res.data.pr;
            // 旧データ形式互換。後日削除OK
            const paks = res.data.paks || res.data;
            Object.keys(paks).forEach((key) => {
              if (contents[key] === undefined || contents[key].articles === undefined) {
                throw new Error(`missing key:${key}`);
              }
              contents[key].articles = paks[key];
              articleCache.addCaches(paks[key]);
            });
          },
          failedMessage: '記事取得に失敗しました',
        });
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetch(); }, { deep: true, immediate: true });
    watch(order, () => { fetch(); });

    return {
      contents,
      handler,
      pr,
    };
  },
});
</script>
