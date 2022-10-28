<template>
  <q-page class="q-ma-md">
    <text-title>マイページトップ</text-title>
    <article-table v-if="mypage.articles" />
  </q-page>
</template>

<script>
import { useMypageStore } from 'src/store/mypage';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import ArticleTable from 'src/components/Mypage/ArticleTable.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';

export default defineComponent({
  name: 'MypageTop',
  setup() {
    const mypage = useMypageStore();
    const auth = useAuthStore();
    if (auth.validateAuth()) {
      mypage.fetchArticles();
    }

    return {
      mypage,
    };
  },
  components: { ArticleTable, TextTitle },
});
</script>
