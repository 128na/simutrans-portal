<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-md-6 col q-gutter-sm" @submit=handler>
      <text-title>ログイン</text-title>
      <api-error-message :message="auth.handler.validationErrorMessage" />
      <template v-if="auth.requireTFA">
        <q-input v-model="code" label="コードまたはリカバリコード" autocomplete="one-time-code" />
        <div>
          <q-btn label="認証" color="primary" type="submit" />
        </div>
      </template>
      <template v-else>
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
      </template>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, reactive, ref } from 'vue';
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
    const code = ref(null);

    const handler = () => {
      if (auth.requireTFA) {
        const param = code.value.length === 6
          ? { code: code.value }
          : { recovery_code: code.value };
        auth.attemptTFA(param);
      } else {
        auth.attemptLogin(state);
      }
    };

    return {
      auth,
      state,
      code,
      handler,
    };
  },
  components: { ApiErrorMessage, InputPassword, TextTitle },
});
</script>
