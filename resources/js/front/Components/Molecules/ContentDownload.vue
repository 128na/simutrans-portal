<template>
  <a class="btn btn-lg btn-primary js-download" :href="article.download_url">ダウンロードする</a>
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  }
};
</script>
