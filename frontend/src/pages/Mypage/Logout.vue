<template>
  <q-page class="q-pa-md">
    <text-title>ログアウト中…</text-title>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/TextTitle.vue';
import { defineComponent } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { useRouter } from 'vue-router';
import { useNotify } from 'src/composables/notify';
import axios from 'axios';
import { useErrorHandler } from 'src/composables/errorHandler';

export default defineComponent({
  name: 'PageLogout',
  components: {
    TextTitle,
  },
  setup() {
    const store = useAuthStore();
    const { postLogout, getToken } = useMypageApi();
    const router = useRouter();
    const notify = useNotify();
    const { errorHandlerStrict } = useErrorHandler();
    const logout = async () => {
      try {
        await postLogout();
        const res = await getToken();
        axios.defaults.headers.common['X-CSRF-TOKEN'] = res.data.token;
        store.logout();
        notify.info('ログアウトしました');
        router.push({ name: 'login' });
      } catch (err) {
        errorHandlerStrict(err);
      }
    };
    logout();
  },
});
</script>
