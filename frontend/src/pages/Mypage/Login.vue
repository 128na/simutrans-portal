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
        <router-link :to="{name:'reset'}" class="default-link">パスワードリセット</router-link>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, reactive, ref } from 'vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useRoute, useRouter } from 'vue-router';
import InputPassword from 'src/components/Common/InputPassword.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { useNotify } from 'src/composables/notify';

export default defineComponent({
  name: 'MypageLogin',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const store = useAuthStore();
    if (store.isLoggedIn) {
      router.replace({ name: 'mypage' });
    }

    const authState = reactive({ email: '', password: '', remember: false });
    const loading = ref(false);

    const notify = useNotify();
    const { errorMessage, errorHandlerStrict } = useErrorHandler(useRouter());
    const { postLogin } = useMypageApi();
    const handle = async () => {
      loading.value = true;
      try {
        const res = await postLogin(authState);
        if (res.status === 200) {
          notify.success('ログインしました');
          store.loggedin(res.data.data);
          router.push(route.query.redirect || { name: 'mypage' });
        }
      } catch (err) {
        errorHandlerStrict(err);
      } finally {
        loading.value = false;
      }
    };
    return {
      authState, handle, errorMessage, loading,
    };
  },
  components: { ApiErrorMessage, InputPassword, TextTitle },
});
</script>
