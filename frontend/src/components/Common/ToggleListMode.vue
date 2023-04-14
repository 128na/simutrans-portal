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
import { useListModeStore } from 'src/store/listMode';

const modes = [
  {
    value: 'gallery', icon: 'image', label: 'ギャラリー', description: 'サムネイル画像をメインに表示します。',
  },
  {
    value: 'list', icon: 'list', label: 'リスト', description: 'サムネイル、タイトル、本文をいい感じに表示します。',
  },
  {
    value: 'show', icon: 'subject', label: '詳細', description: '記事内容をすべて表示します。',
  },
];
export default defineComponent({
  name: 'ToggleListMode',
  setup() {
    const store = useListModeStore();
    return {
      modes,
      store,
    };
  },
});
</script>
