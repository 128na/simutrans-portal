<template>
  <img v-if="article.thumbnail_url" :src="article.thumbnail_url" class="img-fluid thumbnail mb-4 shadow-sm" />
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
