<template>
  <span>{{ text }}</span>
</template>
<script>
export default {
  props: {
    text: {
      type: String,
      required: false
    }
  }
};
</script>
<style scoped>
span {
  white-space: pre-line;
}
</style>
