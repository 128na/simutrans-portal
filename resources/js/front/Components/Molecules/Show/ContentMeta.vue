<template>
  <div>
    <span>投稿：{{ article.published_at }}</span>
    <span>更新：{{ article.modified_at || article.published_at }}</span>
  </div>
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
