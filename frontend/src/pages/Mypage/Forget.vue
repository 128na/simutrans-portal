<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>パスワード再設定メール送信</text-title>
      <api-error-message :message="errorMessage" />
      <q-input v-model="state.email" type="email" label="email" autocomplete="email" />
      <div>
        <q-btn label="送信" color="primary" type="submit" />
      </div>
    </q-form>
  </q-page>
</template>
<script>
import { useQuasar } from 'quasar';
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useNotify } from 'src/composables/notify';
import { defineComponent, reactive } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';

export default defineComponent({
  name: 'MypageForget',
  components: { TextTitle, ApiErrorMessage },
  setup() {
    const $q = useQuasar();
    const state = reactive({ email: '', password: '', remember: false });
    const notify = useNotify();

    const { forget } = useMypageApi();
    const { errorHandlerStrict, clearErrorMessage, errorMessage } = useErrorHandler();
    const handle = async () => {
      $q.loading.show();
      try {
        const res = await forget(state);
        if (res.status === 200) {
          clearErrorMessage();
          notify.success('メールを送信しました');
        }
      } catch (err) {
        errorHandlerStrict(err);
      } finally {
        $q.loading.hide();
      }
    };
    return {
      state, handle, errorMessage,
    };
  },
});
</script>
