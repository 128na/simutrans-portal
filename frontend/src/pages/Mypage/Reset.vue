<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>パスワード再設定</text-title>
      <api-error-message :message="errorMessage" />
      <q-input v-model="state.email" type="email" label="email" autocomplete="email" />
      <input-password v-model="state.password" label="new password" autocomplete="new-password" />
      <div>
        <q-btn label="送信" color="primary" type="submit" />
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
  name: 'MypageReset',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const store = useAuthStore();
    if (store.isLoggedIn) {
      router.replace({ name: 'mypage' });
    }

    const state = reactive({ email: '', password: '', token: route.params.token });
    const loading = ref(false);

    const notify = useNotify();
    const { errorMessage, errorHandlerStrict } = useErrorHandler(useRouter());
    const { reset } = useMypageApi();
    const handle = async () => {
      loading.value = true;
      try {
        const res = await reset(state);
        if (res.status === 200) {
          notify.success('パスワードを更新しました');
          store.logout();
          router.push({ name: 'login' });
        }
      } catch (err) {
        errorHandlerStrict(err);
      } finally {
        loading.value = false;
      }
    };
    return {
      state, handle, errorMessage, loading,
    };
  },
  components: { ApiErrorMessage, InputPassword, TextTitle },
});
</script>
