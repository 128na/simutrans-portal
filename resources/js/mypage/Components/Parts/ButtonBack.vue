<template>
  <div class="mb-2">
    <router-link :to="to">
      <b-icon icon="arrow-left" />
      <slot>{{$t('Back to Index')}}</slot>
    </router-link>
  </div>
</template>
<script>
export default {
  name: "button-back",
  props: {
    name: {
      type: String,
      default: "index"
    },
    params: {
      type: Object,
      default() {
        return {};
      }
    }
  },
  computed: {
    to() {
      return { name: this.name, params: this.params };
    }
  }
};
</script>
