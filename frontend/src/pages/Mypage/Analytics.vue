<template>
  <q-page v-if="mypage.articlesReady" class="q-ma-md">
    <text-title>アクセス解析</text-title>
    <analytics-graph />
    <text-sub-title>設定</text-sub-title>
    <analytics-form />
    <text-sub-title>対象</text-sub-title>
    <analytics-table />
  </q-page>
</template>
<script>
import { useMypageStore } from 'src/store/mypage';
import { defineComponent } from 'vue';
import { useAuthStore } from 'src/store/auth';
import AnalyticsTable from 'src/components/Mypage/AnalyticsTable.vue';
import AnalyticsForm from 'src/components/Mypage/AnalyticsForm.vue';
import AnalyticsGraph from 'src/components/Mypage/AnalyticsGraph.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';
import TextSubTitle from 'src/components/Common/TextSubTitle.vue';

export default defineComponent({
  name: 'PageAnyltics',
  components: {
    AnalyticsGraph, AnalyticsForm, AnalyticsTable, TextTitle, TextSubTitle,
  },
  setup() {
    const auth = useAuthStore();
    const mypage = useMypageStore();
    if (auth.validateAuth()) {
      mypage.fetchArticles();
    }

    return {
      mypage,
    };
  },
});
</script>
