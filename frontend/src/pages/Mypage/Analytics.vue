<template>
  <q-page v-if="mypage.articlesReady">
    <analytics-graph />
    <analytics-form />
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

export default defineComponent({
  name: 'PageAnyltics',
  components: { AnalyticsGraph, AnalyticsForm, AnalyticsTable },
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
