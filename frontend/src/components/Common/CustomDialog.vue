<template>
  <q-card>
    <q-toolbar class="bg-primary text-white">
      <div>
        <slot name="title" />
      </div>
      <q-space />
      <q-btn flat round dense icon="close" class="q-ml-sm" v-close-popup />
    </q-toolbar>
    <slot name="body" />
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'CustomDialog',
  setup() {
  },
});
</script>
