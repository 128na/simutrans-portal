<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>ユーザー登録</text-title>
      <api-error-message :message="errorMessage" />
      <q-input v-model="authState.name" label="name" />
      <q-input v-model="authState.email" type="email" label="email" autocomplete="email" />
      <input-password v-model="authState.password" label="password" autocomplete="new-password" />
      <div>
        <q-btn label="登録" color="primary" type="submit" />
      </div>
    </q-form>
  </q-page>
</template>
<script>
import { useQuasar } from 'quasar';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useNotify } from 'src/composables/notify';
import { useAuthStore } from 'src/store/auth';
import { defineComponent, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import InputPassword from 'src/components/Common/InputPassword.vue';

export default defineComponent({
  name: 'PageInvite',
  components: { TextTitle, ApiErrorMessage, InputPassword },
  setup() {
    const store = useAuthStore();
    store.validateAuth();
    const authState = reactive({ name: '', email: '', password: '' });

    const { invite } = useMypageApi();
    const route = useRoute();
    const router = useRouter();
    const $q = useQuasar();
    const notify = useNotify();
    const { errorHandlerStrict, errorMessage } = useErrorHandler();
    const handle = async () => {
      $q.loading.show();
      try {
        const res = await invite(route.params.code, authState);
        if (res.status === 201) {
          notify.success('登録しました');
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
});
</script>
