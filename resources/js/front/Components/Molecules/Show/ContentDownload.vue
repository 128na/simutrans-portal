<template>
  <a class="btn btn-lg btn-primary" :href="download_url" dusk="conversion-download" @click="handleClick">ダウンロードする</a>
</template>
<script>
import gtm from '../../../../gtm';
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
  },
  methods: {
    handleClick() {
      gtm.frontConversionDownload();
    }
  }
};
</script>
