<template>
  <label-optional>更新日時</label-optional>
  <q-checkbox v-model="editor.withoutUpdateModifiedAt" label="記事保存時に更新日時を更新しない" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent } from 'vue';
import LabelOptional from '../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormWithoutUpdateModifiedAt',
  components: { LabelOptional },
  setup() {
    const editor = useArticleEditStore();
    return {
      editor,
    };
  },
});
</script>
