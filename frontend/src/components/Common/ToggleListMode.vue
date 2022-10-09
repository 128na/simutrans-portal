<template>
  <div @click="store.nextMode">
    <q-icon :name="store.listMode.icon" size="sm" class="cursor-pointer" />
    <q-tooltip>{{ store.listMode.tooltip}}</q-tooltip>
  </div>
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
