<template>
  <div class="mb-2">
    <router-link :to="to">
      <b-icon icon="arrow-left" />
      <slot>戻る</slot>
    </router-link>
  </div>
</template>
<script>
export default {
  props: {
    name: {
      type: String,
      default: "index",
    },
    params: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  computed: {
    to() {
      return { name: this.name, params: this.params };
    },
  },
};
</script>
