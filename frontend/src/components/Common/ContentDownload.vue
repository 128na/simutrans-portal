<template>
  <q-btn color="primary" dusk="conversion-download" @click="handleClick">ダウンロードする</q-btn>
</template>
<script>
import { useAppInfo } from 'src/composables/appInfo';
import { defineComponent } from 'vue';
import gtm from '../../boot/gtm';

export default defineComponent({
  name: 'ContentDownload',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const { backendUrl } = useAppInfo();
    return {
      handleClick() {
        window.open(`${backendUrl}/articles/${props.article.slug}/download`);
        gtm.frontConversionDownload();
      },
    };
  },
});
</script>
