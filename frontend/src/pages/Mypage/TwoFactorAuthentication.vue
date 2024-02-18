<template>
  <q-page class="q-ma-md">
    <text-title>2要素認証</text-title>
    <template v-if="store.user.two_factor">
      設定済みです。
      <q-btn class="q-mb-sm" color="negative" @click="deleteTFA">無効化する</q-btn>
      <template v-if="recoveryCodes">
        <div>2要素認証ができなくなった時のためのリカバリコードを保存してください。ページを閉じるとコードは再表示されません。</div>
        <q-input v-model="recoveryCodes" label="リカバリコード" readonly autogrow type="textarea" />
      </template>
    </template>
    <template v-else>
      <q-btn @click="setupTFA" class="q-mb-sm" color="primary">2要素認証を設定する</q-btn>
      <template v-if="qrCode">
        <div v-html="qrCode" class="q-mb-sm" />
        <api-error-message :message="store.handler.validationErrorMessage" />
        <q-input v-model="code" maxlength="6" label="コード">
          <template v-slot:append>
            <q-btn flat color="primary" @click="confirmTFA">認証</q-btn>
          </template>
        </q-input>
        <div>QRコードを読み取り、表示されるコードを入力してください。</div>
      </template>
    </template>
  </q-page>
</template>
<script>
import { useAuthStore } from 'src/store/auth';
import { defineComponent, ref } from 'vue';
import { useMeta } from 'src/composables/meta';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';

export default defineComponent({
  name: 'TwoFactorAuthentication',
  components: {
    TextTitle,
    ApiErrorMessage,
  },
  setup() {
    const store = useAuthStore();
    store.validateAuth();

    const meta = useMeta();
    meta.setTitle('2要素認証');

    const qrCode = ref(null);
    const code = ref(null);
    const recoveryCodes = ref(null);

    const setupTFA = () => {
      store.setupTFAQrCode().then((data) => { qrCode.value = data; });
    };

    const confirmTFA = async () => {
      recoveryCodes.value = await store.confirmTFACode(code.value);
    };

    const deleteTFA = () => {
      // eslint-disable-next-line no-alert
      if (!window.confirm('無効化しますか？')) {
        return;
      }
      store.deleteTFA();
    };

    return {
      qrCode,
      setupTFA,
      deleteTFA,
      code,
      store,
      confirmTFA,
      recoveryCodes,
    };
  },
});
</script>
