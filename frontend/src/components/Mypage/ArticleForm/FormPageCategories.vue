<template>
  <label-optional>カテゴリ</label-optional>
  <q-option-group v-model="selected" :options="editor.page" type="checkbox" inline />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelOptional from '../../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormPageCategories',
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
