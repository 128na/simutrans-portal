<template>
  <h3 class="text-h3">
    <slot />
  </h3>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TextSubTitle',
});
</script>
