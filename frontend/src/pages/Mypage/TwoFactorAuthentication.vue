<template>
  <q-page class="q-ma-md">
    <text-title>2要素認証</text-title>
    <template v-if="store.user.two_factor">
      <q-btn class="q-mb-sm" color="primary">設定済み</q-btn>
    </template>
    <template v-else>
      <q-btn @click="setupTFA" class="q-mb-sm" color="primary">2要素認証を設定する</q-btn>
    </template>
    <template v-if="qrCode">
      <div v-html="qrCode" class="q-mb-sm" />
      <q-input v-model="code" maxlength="6" label="コード">
        <template v-slot:append>
          <q-btn flat color="primary" @click="store.confirmTFACode(code)">認証</q-btn>
        </template>
      </q-input>
      <div>QRコードを読み取り、表示されるコードを入力してください。</div>
    </template>
  </q-page>
</template>
<script>
import { useAuthStore } from 'src/store/auth';
import { defineComponent, ref } from 'vue';
import { useMeta } from 'src/composables/meta';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';

export default defineComponent({
  name: 'TwoFactorAuthentication',
  components: {
    TextTitle,
  },
  setup() {
    const store = useAuthStore();
    store.validateAuth();

    const meta = useMeta();
    meta.setTitle('2要素認証');

    const qrCode = ref(null);
    const code = ref(null);

    const setupTFA = () => {
      store.setupTFAQrCode().then((data) => { qrCode.value = data; });
    };

    return {
      qrCode,
      setupTFA,
      code,
      store,
    };
  },
});
</script>
