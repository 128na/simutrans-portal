<template>
  <q-page class="q-pa-md fit row wrap justify-center">
    <q-form class="col-6 q-gutter-sm" @submit=handle>
      <text-title>パスワード再設定メール送信</text-title>
      <api-error-message :message="errorMessage" />
      <q-input v-model="state.email" type="email" label="email" autocomplete="email" />
      <div>
        <q-btn label="送信" color="primary" type="submit" />
      </div>
    </q-form>
  </q-page>
</template>
<script>
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { defineComponent, reactive, ref } from 'vue';

export default defineComponent({
  name: 'MypageForget',
  components: {},
  setup() {
    const state = reactive({ email: '', password: '', remember: false });
    const loading = ref(false);
    const notify = useNotify();

    const { forget } = useMypageApi();
    const handle = async () => {
      loading.value = true;
      try {
        const res = await forget(state);
        if (res.status === 200) {
          notify.success('メールを送信しました');
        }
      } catch (err) {
        notify.success('メール送信に失敗しました');
      } finally {
        loading.value = false;
      }
    };
    return {
      state, handle, loading,
    };
  },
});
</script>
