<template>
  <q-btn color="primary" dusk="conversion-download" class="q-mb-md" @click="handleClick"
    :disable="!article.id">ダウンロードする</q-btn>

  <p v-show="article.id">
    ダウンロードできない場合はこちら <a :href="link">{{ link }}</a>
  </p>
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
    const link = props.article.download || `/articles/${props.article.id}/download`;
    return {
      link,
      handleClick() {
        window.open(link);
      },
    };
  },
});
</script>
