<template>
<div>
  <b-badge
   v-for="category in article.categories"
   :key="category.name"
   :href="category.url"
   variant="secondary"
   class="mr-2"
   >
    {{category.name}}
   </b-badge>
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
