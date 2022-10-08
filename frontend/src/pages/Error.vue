<template>
  <q-img src="/images/404.png" fit="cover" class="fullscreen" position="0 0"
    alt="ページが表示できない悲しみを再開発で線路が寸断され、大量発生した「ルート無し」のスクリーンショットで示しています">
    <div class="fullscreen text-center q-pa-md flex flex-center">
      <div>
        <div style="font-size: 10rem">
          {{status}}
        </div>

        <div class="text-h2 q-mb-xl">
          {{message}}
        </div>

        <q-btn size="lg" color="primary" :to="{name:'top'}" label="トップへ" />
      </div>
    </div>
  </q-img>
</template>

<script>
import { defineComponent } from 'vue';
import { useRoute } from 'vue-router';

const getMessage = (status) => {
  if (Number.isNaN(status)) {
    return 'にゃーん🐈';
  }
  switch (status) {
    case 401:
      return 'ログインが必要です。';
    case 403:
      return '権限が有りません。';
    case 404:
      return 'ページが見つかりませんでした。';
    case 418:
      return '( ´･ω･)⊃旦';
    case 419:
      return 'ページの更新期限が切れました';
    case 429:
      return 'アクセス頻度が高すぎます。ゆっくりしていってね！！！';
    default:
      return 'エラーが発生しました。';
  }
};

export default defineComponent({
  name: 'Error',
  setup() {
    const route = useRoute();
    const status = Number.parseInt(route.params.status || 404, 10);
    const message = getMessage(status);

    return {
      status,
      message,
    };
  },
});
</script>
