<template>
  <a class="btn btn-lg btn-primary js-download" :href="download_url">ダウンロードする</a>
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  computed: {
    download_url() {
      return `${this.article.url}/download`;
    }
  }
};
</script>
