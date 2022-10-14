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
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';

export default defineComponent({
  name: 'MypageRequiresVerified',
  components: { TextTitle },
  setup() {
    const { resend } = useMypageApi();
    const notify = useNotify();
    const handle = async () => {
      try {
        await resend();
        notify.success('メールを送信しました');
      } catch (error) {
        notify.failed('メールの送信に失敗しました');
      }
    };
    return { handle };
  },
});
</script>
