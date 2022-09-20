<template>
  <div v-if="preview">
    <b-badge v-for="c in article.categories" :key="key(c)" variant="secondary" class="text-white mr-2">
      {{ c.name }}
    </b-badge>
  </div>
  <div v-else>
    <b-badge v-for="c in article.categories" :key="key(c)" :to="toCategory(c)" variant="secondary"
      class="text-white mr-2">
      {{ c.name }}
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
  methods: {
    key(category) {
      return `${category.type}-${category.slug}`;
    }
  }
};
</script>
