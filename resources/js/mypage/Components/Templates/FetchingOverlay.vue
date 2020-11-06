<template>
  <b-overlay
    :show="fetching"
    rounded
    opacity="0.6"
    spinner-small
    spinner-variant="primary"
    class="d-inline-block"
  >
    <slot />
  </b-overlay>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  computed: {
    ...mapGetters(["fetching"]),
  },
};
</script>
