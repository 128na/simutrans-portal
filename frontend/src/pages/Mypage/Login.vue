<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm">
      <api-error-message :message="errorMessage" />
      <q-input v-model="authState.email" filled type="email" label="email" autocomplete="email" />
      <input-password v-model="authState.password" filled label="password" autocomplete="current-password" />
      <div>
        <q-checkbox v-model="authState.remember" label="ログインしたままにする" />
      </div>
      <div>
        <q-btn label="ログイン" color="primary" @click=handle />
      </div>
      <div>
        <router-link :to="{name:'reset'}">パスワードリセット</router-link>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, reactive, ref } from 'vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useRouter } from 'vue-router';
import InputPassword from 'src/components/Common/InputPassword.vue';
import { api } from '../../boot/axios';

export default defineComponent({
  name: 'MypageLogin',
  setup() {
    const authState = reactive({ email: '', password: '', remember: false });
    const loading = ref(false);

    const { errorMessage, errorHandlerStrict } = useErrorHandler(useRouter());
    const handle = async () => {
      loading.value = true;
      try {
        const res = await api.post('/api/v2/login', authState);
        if (res.status === 200) {
          // console.log('ok');
        }
      } catch (err) {
        errorHandlerStrict(err);
      } finally {
        loading.value = false;
      }
    };
    return { authState, handle, errorMessage };
  },
  components: { ApiErrorMessage, InputPassword },
});
</script>
