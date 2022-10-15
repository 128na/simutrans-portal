<template>
  <q-page>
    <article-table v-if="store.articles" />
  </q-page>
</template>

<script>
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useMypageStore } from 'src/store/mypage';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import ArticleTable from 'src/components/Mypage/ArticleTable.vue';

export default defineComponent({
  name: 'MypageTop',
  setup() {
    const { fetchArticles } = useMypageApi();
    const { errorHandlerStrict } = useErrorHandler();
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
    const auth = useAuthStore();
    if (auth.validateAuth()) {
      fetch();
    }

    return {
      store,
    };
  },
  components: { ArticleTable },
});
</script>
