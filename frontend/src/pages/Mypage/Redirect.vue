<template>
  <q-page class="q-ma-md">
    <RedirectList />
  </q-page>
</template>
<script>
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import { useMeta } from 'src/composables/meta';
import RedirectList from 'src/components/Mypage/RedirectList.vue';

export default defineComponent({
  name: 'PageRedirect',
  components: {
    RedirectList,
  },
  setup() {
    const store = useAuthStore();
    store.validateAuth();

    const meta = useMeta();
    meta.setTitle('設定済みリダイレクト一覧');
  },
});
</script>
