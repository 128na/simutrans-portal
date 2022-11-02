<template>
  <h2 class="text-h2">
    <slot />
  </h2>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TextTitle',
});
</script>
