<template>
  <q-page class="q-ma-md">
    <text-title>マイページトップ</text-title>
    <template v-if="mypage.articles">
      <article-export />
      <article-table />
    </template>
  </q-page>
</template>

<script>
import { useMypageStore } from 'src/store/mypage';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import ArticleTable from 'src/components/Mypage/ArticleTable.vue';
import ArticleExport from 'src/components/Mypage/ArticleExport.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';

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
  components: { ArticleTable, ArticleExport, TextTitle },
});
</script>
