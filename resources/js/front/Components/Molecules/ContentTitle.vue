<template>
  <h3 class="title border-bottom mb-4">{{article.title}}</h3>
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
