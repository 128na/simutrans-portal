<template>
  <q-img src="/images/404.png" fit="cover" class="fullscreen">
    <div class="fullscreen text-center q-pa-md flex flex-center">
      <div>
        <div style="font-size: 30vh">
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
  switch (status) {
    case '401':
      return '権限が有りません。';
    case '404':
      return 'ページが見つかりませんでした。';
    case '419':
      return 'エラーが発生しました。';
    case '429':
      return 'アクセス頻度が高すぎます。';
    default:
      return 'エラーが発生しました。';
  }
};

export default defineComponent({
  name: 'Error',
  setup() {
    const route = useRoute();
    const status = String(route.params.status || 404);
    const message = getMessage(status);

    return {
      status,
      message,
    };
  },
});
</script>
