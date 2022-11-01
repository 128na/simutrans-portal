<template>
  <input-countable label v-model="editor.article.contents.markdown" rows="12" :maxLength="65535">
    <label-required>内容</label-required>
  </input-countable>
  <form-page-categories />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import FormPageCategories from 'src/components/Mypage/FormPageCategories.vue';
import InputCountable from 'src/components/Common/InputCountable.vue';

export default defineComponent({
  name: 'FormMarkdown',
  components: {
    InputCountable,
    LabelRequired,
    FormPageCategories,
  },
  setup() {
    const editor = useArticleEditStore();

    return {
      editor,
    };
  },
});
</script>
