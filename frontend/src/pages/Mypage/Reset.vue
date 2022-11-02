<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>パスワード再設定</text-title>
      <api-error-message :message="handler.validationErrorMessage.value" />
      <q-input v-model="state.email" type="email" label="email" autocomplete="email" />
      <input-password v-model="state.password" label="new password" autocomplete="new-password" />
      <div>
        <q-btn label="送信" color="primary" type="submit" />
      </div>
    </q-form>
  </q-page>
</template>
<script>
import { defineComponent, reactive } from 'vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';
import { useRoute } from 'vue-router';
import InputPassword from 'src/components/Common/Input/InputPassword.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { useNotify } from 'src/composables/notify';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'MypageReset',
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();

    const route = useRoute();
    const state = reactive({ email: '', password: '', token: route.params.token });

    const notify = useNotify();
    const api = useMypageApi();
    const handler = useApiHandler();
    const handle = async () => {
      try {
        await handler.handleWithValidate(() => api.reset(state));
        notify.success('パスワードを更新しました');
        auth.attemptLogout();
      } catch {
        // do nothing.
      }
    };
    return {
      state, handle, handler,
    };
  },
  components: { ApiErrorMessage, InputPassword, TextTitle },
});
</script>
