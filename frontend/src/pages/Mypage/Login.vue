<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>ログイン</text-title>
      <api-error-message :message="errorMessage" />
      <q-input v-model="authState.email" type="email" label="email" autocomplete="email" />
      <input-password v-model="authState.password" label="password" autocomplete="current-password" />
      <div>
        <q-checkbox v-model="authState.remember" label="ログインしたままにする" />
      </div>
      <div>
        <q-btn label="ログイン" color="primary" type="submit" />
      </div>
      <div>
        <router-link :to="{name:'forget'}" class="default-link">パスワードリセット</router-link>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, reactive } from 'vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useRoute, useRouter } from 'vue-router';
import InputPassword from 'src/components/Common/InputPassword.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { useNotify } from 'src/composables/notify';
import { useQuasar } from 'quasar';

export default defineComponent({
  name: 'MypageLogin',
  setup() {
    const $q = useQuasar();
    const store = useAuthStore();
    store.validateAuth();

    const authState = reactive({ email: '', password: '', remember: false });

    const notify = useNotify();
    const { errorMessage, errorHandlerStrict } = useErrorHandler(useRouter());
    const { postLogin } = useMypageApi();
    const route = useRoute();
    const router = useRouter();
    const handle = async () => {
      $q.loading.show();
      try {
        const res = await postLogin(authState);
        if (res.status === 200) {
          notify.success('ログインしました');
          store.login(res.data.data);
          router.push(route.query.redirect || { name: 'mypage' });
        }
      } catch (err) {
        errorHandlerStrict(err);
      } finally {
        $q.loading.hide();
      }
    };
    return {
      authState, handle, errorMessage,
    };
  },
  components: { ApiErrorMessage, InputPassword, TextTitle },
});
</script>
