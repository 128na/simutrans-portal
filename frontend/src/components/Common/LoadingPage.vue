<template>
  <q-page class="flex flex-center">
    <div class="q-mb-md text-h1">
      <q-spinner />
      Loading...
    </div>
  </q-page>
</template>
<script>

import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LoadingPage',
});
</script>
