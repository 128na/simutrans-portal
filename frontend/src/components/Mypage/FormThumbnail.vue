<template>
  <label-optional>サムネイル画像</label-optional>
  <q-input :model-value="filename" readonly>
    <template v-slot:append>
      <file-manager v-model="selected" />
    </template>
  </q-input>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import { defineComponent, ref, computed } from 'vue';
import FileManager from './FileManager.vue';

export default defineComponent({
  name: 'FormThumbnail',
  components: { LabelOptional, FileManager },
  setup() {
    const editor = useArticleEditStore();
    const selected = ref([]);
    const filename = computed(() => (selected.value.length ? selected.value.join(', ') : '未選択'));
    return {
      editor,
      selected,
      filename,
    };
  },
});
</script>
