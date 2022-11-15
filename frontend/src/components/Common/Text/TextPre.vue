<template>
  <span class="text-pre">
    <slot />
  </span>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TextPre',
});
</script>
<style scoped>
.text-pre {
  white-space: pre-line;
  word-break: break-word;
}
</style>
