<template>
  <text-pre v-if="preview" :text="user.name" />
  <router-link v-else :to="toUser(user)">
    <text-pre :text="user.name" />
  </router-link>
</template>
<script>
import { routeLink } from '../../../mixins';
export default {
  mixins: [routeLink],
  props: {
    user: {
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
