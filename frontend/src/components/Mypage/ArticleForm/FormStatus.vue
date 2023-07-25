<template>
  <label-required>公開状態</label-required>
  <q-btn-toggle v-model="editor.article.status" :options="options" @update:model-value="handle" name="status" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import { defaultDateTime } from 'src/const';
import LabelRequired from 'src/components/Common/LabelRequired.vue';

export default defineComponent({
  name: 'FormStatus',
  components: { LabelRequired },
  setup() {
    const editor = useArticleEditStore();
    const options = computed(() => (editor.canReservation
      ? editor.options.statuses
      : editor.options.statuses.filter((s) => s.value !== 'reservation')));

    const handlePublishedAt = (val) => {
      // 予約投稿のときは投稿日時に入力値をセットする
      if (val === 'reservation') {
        editor.article.published_at = defaultDateTime().toISO();
      } else {
        editor.article.published_at = null;
      }
    };
    const handleNotify = (val) => {
      // 公開のとき以外は無効にする
      if (val !== 'publish') {
        editor.shouldNotify = false;
      }
    };
    const handle = (val) => {
      handlePublishedAt(val);
      handleNotify(val);
    };
    return {
      editor,
      options,
      handle,
    };
  },
});
</script>
