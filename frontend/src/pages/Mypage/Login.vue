<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-md-6 col q-gutter-sm" @submit=auth.attemptLogin(state)>
      <text-title>ログイン</text-title>
      <api-error-message :message="auth.handler.validationErrorMessage" />
      <q-input v-model="state.email" type="email" label="email" autocomplete="email" />
      <input-password v-model="state.password" label="password" autocomplete="current-password" />
      <div>
        <q-checkbox v-model="state.remember" label="ログインしたままにする" />
      </div>
      <div>
        <q-btn label="ログイン" color="primary" type="submit" />
      </div>
      <div>
        <router-link :to="{ name: 'forget' }" class="default-link">パスワードリセット</router-link>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, reactive } from 'vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';
import InputPassword from 'src/components/Common/Input/InputPassword.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useAuthStore } from 'src/store/auth';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'MypageLogin',
  setup() {
    const auth = useAuthStore();
    if (auth.validateAuth()) {
      auth.initializeCsrf();
    }
    const meta = useMeta();
    meta.setTitle('ログイン');

    const state = reactive({ email: '', password: '', remember: true });

    return {
      auth, state,
    };
  },
  components: { ApiErrorMessage, InputPassword, TextTitle },
});
</script>
