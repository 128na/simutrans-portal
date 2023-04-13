<template>
  <q-btn-toggle v-model="store.currentMode" toggle-color="primary" :options="store.listModes" />
</template>
<script>

import { defineComponent } from 'vue';
import { useListModeStore } from 'src/store/listMode';

export default defineComponent({
  name: 'ToggleListMode',
  setup() {
    const store = useListModeStore();
    return {
      store,
    };
  },
});
</script>
