<template>
  <div><label for="status">公開状態</label></div>
  <q-btn-toggle v-model="editor.article.status" :options="options" @update:model-value="handle" name="status" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import { defaultDateTime } from 'src/const';

export default defineComponent({
  name: 'FormStatus',
  components: {},
  setup() {
    const editor = useArticleEditStore();
    const options = computed(() => (editor.canReservation
      ? editor.options.statuses
      : editor.options.statuses.filter((s) => s.value !== 'reservation')));

    const handle = (val) => {
      // 予約投稿のときは投稿日時に入力値をセットする
      if (val === 'reservation') {
        editor.article.published_at = defaultDateTime().toISO();
      } else {
        editor.article.published_at = null;
      }
    };
    return {
      editor,
      options,
      handle,
    };
  },
});
</script>
