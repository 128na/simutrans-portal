<template>
  <q-expansion-item label="投稿記事エクスポート" switch-toggle-side>
    <div class="q-ma-sm">
      投稿した記事（アドオン紹介、投稿のみ）を一括でダウンロードできます。<br>
      記事数が多いとファイルの生成には数分かかることがあります。<br>
      <template v-if="idle">
        <q-btn color="primary" label="エクスポート" @click="bz.fetch" />
      </template>
      <template v-else-if="bz.inProgress">
        <loading-message>
          ファイル生成中...画面を開いたままお待ちください。
        </loading-message>
      </template>
      <template v-else-if="!!bz.generated">
        ファイルが作成されました。作成日時：{{ bz.generated.generated_at }}<br>
        <q-btn color="primary" @click="handle">ダウンロード</q-btn>
      </template>
    </div>
  </q-expansion-item>
</template>

<script>
import { useBulkZipStore } from 'src/store/bulkZip';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ArticleExport',
  setup() {
    const bz = useBulkZipStore();

    const idle = computed(() => bz.inProgress === false && bz.generated === null);
    const handle = () => {
      window.open(bz.generated.url);
    };
    return {
      bz,
      idle,
      handle,
    };
  },
  components: { LoadingMessage },
});
</script>
