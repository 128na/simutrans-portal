<template>
  <div v-if="preview">
    <b-badge v-for="category in article.categories" :key="category.name" variant="secondary" class="mr-2">
      {{ category.name }}
    </b-badge>
  </div>
  <div v-else>
    <b-badge v-for="category in article.categories" :key="category.name" :to="toCategory(category)" variant="secondary"
      class="mr-2">
      {{ category.name }}
    </b-badge>
  </div>
</template>
<script>
import { routeLink } from '../../../mixins';
export default {
  mixins: [routeLink],
  props: {
    article: {
      type: Object,
      required: true
    },
    preview: {
      type: Boolean,
      default: false
    }
  },
  computed: {

  }
};
</script>
