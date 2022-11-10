<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-md-6 col q-gutter-sm" @submit=handle>
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
import { useApiHandler } from 'src/composables/apiHandler';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'MypageReset',
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();

    const meta = useMeta();
    meta.setTitle('パスワード再設定');

    const route = useRoute();
    const state = reactive({ email: '', password: '', token: route.params.token });

    const api = useMypageApi();
    const handler = useApiHandler();
    const handle = async () => {
      try {
        await handler.handleWithValidate({
          doRequest: () => api.reset(state),
          done: () => {
            auth.attemptLogout();
          },
          successMessage: 'パスワードを更新しました',
        });
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
