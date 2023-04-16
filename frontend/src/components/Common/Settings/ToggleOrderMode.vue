<template>
  <q-list>
    <q-item tag="label" v-ripple v-for="(mode, key) in modes" :key="key">
      <q-item-section avatar>
        <q-radio v-model="store.currentMode" :val="mode.value" />
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

import { defineComponent } from 'vue';
import { useOrderModeStore } from 'src/store/listMode';

const modes = [
  {
    value: 'modified_at', label: '更新日順', description: '更新日の新しい順に表示します。',
  },
  {
    value: 'published_at', label: '投稿日順', description: '投稿日の新しい順に表示します。',
  },
];
export default defineComponent({
  name: 'ToggleOrderMode',
  setup() {
    const store = useOrderModeStore();
    return {
      modes,
      store,
    };
  },
});
</script>
