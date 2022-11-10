<template>
  <q-page class="q-pa-md">
    <q-form @submit=handle>
      <text-title>メールアドレスの確認が済んでいません</text-title>
      <p>
        全ての機能を使用するにはメールアドレスの確認が必要です。<br>
        確認メールを再送信するには下のボタンをクリックしてください。
      </p>
      <q-btn color="primary" type="submit">
        確認メールを再送信する
      </q-btn>
    </q-form>
  </q-page>
</template>
<script>
import { defineComponent } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { useApiHandler } from 'src/composables/apiHandler';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'MypageRequiresVerified',
  components: { TextTitle },
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();

    const meta = useMeta();
    meta.setTitle('未認証');

    const api = useMypageApi();
    const handler = useApiHandler();
    const handle = async () => {
      try {
        await handler.handleWithLoading({
          doRequest: () => api.resend(),
          successMessage: 'メールを送信しました',
          failedMessage: 'メールの送信に失敗しました',
        });
      } catch {
        // do nothing.
      }
    };
    return { handle };
  },
});
</script>
