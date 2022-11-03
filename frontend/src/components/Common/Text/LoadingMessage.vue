<template>
  <div class="q-mb-md">
    <q-spinner />
    <slot>
      Loading...
    </slot>
  </div>
</template>
<script>

import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LoadingMessage',
});
</script>
