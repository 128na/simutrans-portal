<template>
  <a class="btn btn-lg btn-primary" :href="download_url" dusk="conversion-download">ダウンロードする</a>
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
      return `/articles/${this.article.slug}/download`;
    }
  }
};
</script>
