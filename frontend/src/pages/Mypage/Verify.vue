<template>
  <q-page class="q-pa-md">
    <text-title>確認中…</text-title>
  </q-page>
</template>
<script>
import { defineComponent } from 'vue';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from 'src/store/auth';

export default defineComponent({
  name: 'Verify',
  components: { TextTitle },
  setup() {
    const { verify } = useMypageApi();
    const notify = useNotify();
    const route = useRoute();
    const router = useRouter();
    const store = useAuthStore();
    const handle = async () => {
      try {
        const { userId, hash } = route.params;
        const { expires, signature } = route.query;
        const res = await verify(userId, hash, expires, signature);
        notify.success('認証が完了しました');
        store.user = res.data.data;
        router.push({ name: 'mypage' });
      } catch (error) {
        notify.failed('認証に失敗しました');
      }
    };
    handle();
    return { handle };
  },
});
</script>
