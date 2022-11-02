<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>パスワード再設定メール送信</text-title>
      <api-error-message :message="handler.validationErrorMessage.value" />
      <q-input v-model="state.email" type="email" label="email" autocomplete="email" />
      <div>
        <q-btn label="送信" color="primary" type="submit" />
      </div>
    </q-form>
  </q-page>
</template>
<script>
import { useMypageApi } from 'src/composables/api';
import { defineComponent, reactive } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'MypageForget',
  components: { TextTitle, ApiErrorMessage },
  setup() {
    const state = reactive({ email: '', password: '', remember: false });

    const api = useMypageApi();
    const handler = useApiHandler();
    const handle = async () => {
      try {
        handler.handleWithValidate({ doReqest: () => api.forget(state), successMessage: 'メールを送信しました' });
      } catch {
        // do nothing
      }
    };
    return {
      state, handle, handler,
    };
  },
});
</script>
