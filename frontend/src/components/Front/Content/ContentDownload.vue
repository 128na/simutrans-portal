<template>
  <q-btn color="primary" dusk="conversion-download" @click="handleClick">ダウンロードする</q-btn>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ContentDownload',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return {
      handleClick() {
        window.open(`/articles/${props.article.user.id}/${props.article.slug}/download`);
      },
    };
  },
});
</script>
