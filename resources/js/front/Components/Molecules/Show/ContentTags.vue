<template>
  <div v-if="preview">
    <b-badge v-for="tag in article.tags" :key="tag.name" variant="secondary" class="mr-2">
      {{ tag.name }}
    </b-badge>
  </div>
  <div v-else>
    <b-badge v-for="tag in article.tags" :key="tag.name" :to="toTag(tag)" variant="secondary" class="mr-2">
      {{ tag.name }}
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
  }
};
</script>
