<template>
  <div>{{article.post_type}}</div>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ArticleShowMarkdown',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return {
    };
  },
});
</script>
