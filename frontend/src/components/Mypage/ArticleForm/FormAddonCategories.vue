<template>
  <label-optional>Pak</label-optional>
  <q-option-group v-model="selected" :options="editor.pak" type="checkbox" inline />
  <label-optional>形式</label-optional>
  <q-option-group v-model="selected" :options="editor.addon" type="checkbox" inline />
  <template v-if="editor.includesPak128">
    <label-optional>Pak128用描画位置</label-optional>
    <q-option-group v-model="selected" :options="editor.pak128Position" type="checkbox" inline />
  </template>
  <label-optional>緩急坂</label-optional>
  <q-option-group v-model="selected" :options="editor.doubleSlope" type="checkbox" inline />
  <label-optional>ライセンス</label-optional>
  <q-option-group v-model="selected" :options="editor.license" type="checkbox" inline />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelOptional from '../../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormAddonCategories',
  components: {
    LabelOptional,
  },
  setup() {
    const editor = useArticleEditStore();

    const selected = computed({
      get: () => editor.article.categories.map((c) => c.id),
      set: (val) => {
        editor.article.categories = val.map((c) => editor.getCategory(c));
      },
    });

    const toOption = (categories) => categories.map((c) => ({ value: c.id, label: c.name }));

    return {
      editor,
      selected,
      toOption,
    };
  },
});
</script>
