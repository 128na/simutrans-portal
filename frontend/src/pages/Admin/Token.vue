<template>
  <q-page class="q-ma-md">
    <text-title>Oauth2トークン管理</text-title>

    <div>
      <a href="/admin/oauth/twitter/authorize">認証</a><br>
      <a href="/admin/oauth/twitter/refresh">トークンリフレッシュ</a><br>
      <a href="/admin/oauth/twitter/revoke">トークン削除</a><br>
    </div>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PageAdminToken',
  components: { TextTitle },
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();
  },
});
</script>
