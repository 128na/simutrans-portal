<template>
  <q-page class="q-pa-md">
    <text-title>処理中…</text-title>
  </q-page>
</template>
<script>
import { defineComponent } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from 'src/store/auth';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'Verify',
  components: { TextTitle },
  setup() {
    const api = useMypageApi();
    const route = useRoute();
    const router = useRouter();
    const store = useAuthStore();
    const handler = useApiHandler();
    const handle = async () => {
      try {
        const { userId, hash } = route.params;
        const { expires, signature } = route.query;
        const res = await handler.handleWithLoading({
          doRequest: () => api.verify(userId, hash, expires, signature),
          successMessage: '認証が完了しました',
          failedMessage: '認証に失敗しました',
        });
        store.setUser(res.data.data);
        router.push({ name: 'mypage' });
      } catch {
        // do nothing
      }
    };
    handle();
    return { handle };
  },
});
</script>
