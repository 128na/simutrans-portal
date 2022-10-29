<template>
  <q-input label v-model="editor.article.contents.markdown" rows="12" type="textarea">
    <template v-slot:label>
      <label-required>内容</label-required>
    </template>
  </q-input>
  <form-page-categories />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import FormPageCategories from 'src/components/Mypage/FormPageCategories.vue';

export default defineComponent({
  name: 'FormMarkdown',
  components: {
    LabelRequired, FormPageCategories,
  },
  setup() {
    const editor = useArticleEditStore();

    return {
      editor,
    };
  },
});
</script>
