<template>
  <span>{{text}}</span>
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
