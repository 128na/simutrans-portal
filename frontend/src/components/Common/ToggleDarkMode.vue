<template>
  <q-list>
    <q-item tag="label" v-ripple v-for="(mode, key) in modes" :key="key">
      <q-item-section avatar>
        <q-radio v-model="currentMode" :val="mode.value" />
      </q-item-section>
      <q-item-section>
        <q-item-label>
          {{ mode.label }}</q-item-label>
        <q-item-label caption>{{ mode.description }}</q-item-label>
      </q-item-section>
    </q-item>
  </q-list>
</template>
<script>

import { defineComponent, computed, ref } from 'vue';
import { useQuasar } from 'quasar';

const modes = [
  {
    label: 'デフォルト', icon: 'light_mode', value: false, description: '白背景に黒文字のありふれた世界。',
  },
  {
    label: 'ダークモード', icon: 'dark_mode', value: true, description: '漆黒の闇で世界を覆い尽くします。',
  },
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
