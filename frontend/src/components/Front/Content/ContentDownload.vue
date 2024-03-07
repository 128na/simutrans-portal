<template>
  <q-btn color="primary" dusk="conversion-download" class="q-mb-md" @click="handleClick"
    :disable="!article.id">ダウンロードする</q-btn>
</template>

<script>
import { defineComponent } from 'vue';
import fileDownload from 'js-file-download';
import axios from 'axios';

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
      async handleClick() {
        const res = await axios.post(`/articles/${props.article.id}/download`, {}, { responseType: 'blob' });
        // attachment; filename=original_filename.ext
        const filename = res.headers['content-disposition'].replace('attachment; filename=', '');
        fileDownload(res.data, filename);
      },
    };
  },
});
</script>
