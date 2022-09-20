<template>
  <div v-if="preview">
    <b-badge v-for="t in article.tags" :key="t.id" variant="secondary" class="text-white mr-2">
      {{ t.name }}
    </b-badge>
  </div>
  <div v-else>
    <b-badge v-for="t in article.tags" :key="t.id" :to="toTag(t)" variant="secondary" class="text-white mr-2">
      {{ t.name }}
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
