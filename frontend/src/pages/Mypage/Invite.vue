<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-md-6 col q-gutter-sm" @submit=handle>
      <text-title>ユーザー登録</text-title>
      <api-error-message :message="handler.validationErrorMessage.value" />
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
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { defineComponent, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';
import InputPassword from 'src/components/Common/Input/InputPassword.vue';
import { useApiHandler } from 'src/composables/apiHandler';

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
    const handler = useApiHandler();
    const handle = async () => {
      try {
        await handler.handleWithValidate({
          doRequest: () => invite(route.params.code, authState),
          done: (res) => {
            store.setUser(res.data.data);
            router.push(route.query.redirect || { name: 'mypage' });
          },
          successMessage: '登録しました',
        });
      } catch {
        // do nothing.
      }
    };
    return {
      authState, handle, handler,
    };
  },
});
</script>
