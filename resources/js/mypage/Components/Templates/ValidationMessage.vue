<template>
  <div v-if="messages.length" class="text-danger">
    <div v-for="m in messages" :key="m">{{ m }}</div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["field"],
  computed: {
    ...mapGetters(["getValidationErrors"]),
    messages() {
      return this.getValidationErrors(this.field);
    },
  },
};
</script>
