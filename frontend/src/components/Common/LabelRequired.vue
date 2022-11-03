<template>
  <div class="flex items-center">
    <q-badge color="negative" class="q-mr-xs">必須</q-badge>
    <slot />
  </div>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LabelRequired',
});
</script>
