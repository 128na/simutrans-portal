<template>
  <label-optional>更新日時</label-optional>
  <q-checkbox v-model="withoutUpdateModifiedAt" label="記事保存時に更新日時を更新しない" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelOptional from '../../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormWithoutUpdateModifiedAt',
  components: { LabelOptional },
  setup() {
    const editor = useArticleEditStore();
    const withoutUpdateModifiedAt = computed({
      get: () => editor.withoutUpdateModifiedAt,
      set: (v) => {
        if (v) {
          editor.shouldNotify = false;
        }
        editor.withoutUpdateModifiedAt = v;
      },
    });
    return {
      editor,
      withoutUpdateModifiedAt,
    };
  },
});
</script>
