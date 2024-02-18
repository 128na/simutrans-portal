<template>
  <q-page class="q-ma-md">
    <TwoFactorAuthentication />
    <LoginHistory />
  </q-page>
</template>
<script>
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import { useMeta } from 'src/composables/meta';
import LoginHistory from 'src/components/Mypage/LoginHistory.vue';
import TwoFactorAuthentication from 'src/components/Mypage/TwoFactorAuthentication.vue';

export default defineComponent({
  name: 'SecurityMisc',
  components: {
    LoginHistory,
    TwoFactorAuthentication,
  },
  setup() {
    const store = useAuthStore();
    store.validateAuth();

    const meta = useMeta();
    meta.setTitle('セキュリティ');
  },
});
</script>
