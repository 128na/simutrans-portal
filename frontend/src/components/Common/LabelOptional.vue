<template>
  <div class="flex items-center">
    <q-badge color="secondary" class="q-mr-xs">任意</q-badge>
    <slot />
  </div>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LabelOptional',
});
</script>
