<template>
  <q-page class="q-pa-md">
    <text-title>ログアウト中…</text-title>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { defineComponent } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useAppInfo } from 'src/composables/appInfo';

export default defineComponent({
  name: 'PageLogout',
  components: {
    TextTitle,
  },
  setup() {
    const store = useAuthStore();
    const { postLogout } = useMypageApi();
    const { errorHandlerStrict } = useErrorHandler();
    const logout = async () => {
      try {
        await postLogout();
        store.logout();
        const { appUrl } = useAppInfo();
        window.location.href = appUrl;
      } catch (err) {
        errorHandlerStrict(err);
      }
    };
    logout();
  },
});
</script>
