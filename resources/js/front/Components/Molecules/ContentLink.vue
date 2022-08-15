<template>
  <a
      :href="article.contents.link"
      :data-url="article.contents.link"
      :data-slug="article.slug"
      class="js-click text-primary"
      target="_blank"
      rel="noopener noreferrer"
  >{{ article.contents.link }}</a>
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
