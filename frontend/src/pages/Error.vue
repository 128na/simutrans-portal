<template>
  <q-img :src="error.background" fit="cover" class="fullscreen" :position="error.position">
    <div class="fullscreen text-center q-pa-md flex flex-center">
      <div>
        <div style="font-size: 10rem">
          {{ error.status }}
        </div>

        <div class="text-h2 q-mb-xl">
          {{ error.message }}
        </div>

        <q-btn size="lg" color="primary" :to="error.to" :label="error.label" />
      </div>
    </div>
  </q-img>
</template>

<script>
import { useColor } from 'src/composables/color';
import { defineComponent, reactive } from 'vue';
import { useRoute } from 'vue-router';

const errors = {
  401: {
    status: 401,
    message: 'ログイン期限が切れています。再度ログインしてください',
    to: { name: 'login' },
    label: 'ログイン',
    background: '/images/404.png',
    position: '0 0',
  },
  403: {
    status: 403,
    message: '権限が有りません。諦めてください',
    to: { name: 'top' },
    label: 'トップ',
    background: '/images/404.png',
    position: '0 0',
  },
  404: {
    status: 404,
    message: 'ページが見つかりませんでした。',
    to: { name: 'top' },
    label: 'トップ',
    background: '/images/404.png',
    position: '0 0',
  },
  418: {
    status: 418,
    message: '( ´･ω･)⊃旦',
    to: { name: 'top' },
    label: 'トップ',
    background: '/images/404.png',
    position: '0 0',
  },
  419: {
    status: 419,
    message: '認証トークンの期限が切れました。再度ログインしてください。',
    to: { name: 'login' },
    label: 'トップ',
    background: '/images/404.png',
    position: '0 0',
  },
  422: {
    status: 422,
    message: '入力に問題があります。内容を確認して再度お試しください',
    to: { name: 'login' },
    label: 'ログイン',
    background: '/images/404.png',
    position: '0 0',
  },
  429: {
    status: 429,
    message: 'アクセス頻度が高すぎます。ゆっくりしていってね！！！',
    to: { name: 'top' },
    label: 'トップ',
    background: '/images/429.png',
    position: '50% 100%',
  },
  default: {
    status: 500,
    message: 'エラーが発生しました。',
    to: { name: 'top' },
    label: 'トップ',
    background: '/images/500.png',
    position: '50% 100%',
  },
};

export default defineComponent({
  name: 'Error',
  setup() {
    const color = useColor();
    color.setFront();
    const route = useRoute();
    const status = reactive(Number.parseInt(route.params.status || 404, 10));
    const error = errors[status] || errors.default;

    return {
      status,
      error,
    };
  },
});
</script>
