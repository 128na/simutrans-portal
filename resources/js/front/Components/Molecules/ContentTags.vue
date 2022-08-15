<template>
<div>
  <b-badge
   v-for="tag in article.tags"
   :key="tag.name"
   :href="tag.url"
   variant="secondary"
   class="mr-2"
   >
    {{tag.name}}
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
