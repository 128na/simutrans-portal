<template>
  <q-btn color="primary" dusk="conversion-download" class="q-mb-md" @click="handleClick"
    :disable="!article.id">ダウンロードする</q-btn>

  <p>
    <a :href="link" class="text-secondary">ダウンロードが始まらない場合はこちら</a>
  </p>
</template>

<script>
import { defineComponent, computed } from 'vue';
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
    const link = computed(() => `/articles/${props.article.id}/download`);
    return {
      link,
      async handleClick() {
        const res = await axios.post(link.value, {}, { responseType: 'blob' });
        // attachment; filename=original_filename.ext
        const filename = res.headers['content-disposition']
          .replace('attachment; filename=', '')
          .replace(/^"|"$/g, '');
        fileDownload(res.data, filename);
      },
    };
  },
});
</script>
