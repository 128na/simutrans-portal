<template>
  <q-btn-toggle v-model="currentMode" toggle-color="primary" :options="modes" />
</template>
<script>

import { defineComponent, computed, ref } from 'vue';
import { useQuasar } from 'quasar';

const modes = [
  { label: '暗', icon: 'dark_mode', value: true },
  { label: '明', icon: 'light_mode', value: false },
];

export default defineComponent({
  name: 'ToggleDarkMode',
  setup() {
    const $q = useQuasar();
    const current = ref($q.localStorage.getItem('darkmode') === 'darkmode');
    $q.dark.set(current.value);

    const currentMode = computed({
      get() {
        return current.value;
      },
      set(v) {
        current.value = v;
        $q.dark.set(v);
        $q.localStorage.set('darkmode', v ? 'darkmode' : '');
      },
    });
    return {
      modes,
      currentMode,
    };
  },
});
</script>
