<template>
  <q-page v-if="store.articles">
    <article-table />
  </q-page>
</template>

<script>
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useMypageStore } from 'src/store/mypage';
import { defineComponent } from 'vue';
import ArticleTable from 'src/components/Mypage/ArticleTable.vue';

export default defineComponent({
  name: 'MypageTop',
  setup() {
    const { fetchArticles } = useMypageApi();
    const { errorHandlerStrict, errorMessage } = useErrorHandler();
    const store = useMypageStore();
    const fetch = async () => {
      try {
        const res = await fetchArticles();
        if (res.status === 200) {
          store.articles = res.data.data;
        }
      } catch (err) {
        errorHandlerStrict(err, '');
      }
    };
    if (!store.articles) {
      fetch();
    }

    return {
      errorMessage,
      fetch,
      store,
    };
  },
  components: { ArticleTable },
});
</script>
