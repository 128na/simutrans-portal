<template>
  <q-list>
    <q-item-label header>設定</q-item-label>
    <q-item>
      <q-item-label caption>
        設定はブラウザごとに保存されます。
      </q-item-label>
    </q-item>
    <q-separator />
    <q-item-label header>一覧形式</q-item-label>
    <ToggleListMode />
    <q-separator />
    <q-item-label header>一覧表示順</q-item-label>
    <ToggleOrderMode />
    <q-separator />
    <q-item-label header>テーマカラー</q-item-label>
    <ToggleDarkMode />
    <q-separator />
    <MetaInfo />
  </q-list>
</template>
<script>

import ToggleDarkMode from 'src/components/Common/Settings/ToggleDarkMode.vue';
import ToggleListMode from 'src/components/Common/Settings/ToggleListMode.vue';
import ToggleOrderMode from 'src/components/Common/Settings/ToggleOrderMode.vue';
import { defineComponent } from 'vue';
import MetaInfo from 'src/components/Common/MetaInfo.vue';

export default defineComponent({
  name: 'FrontRightMenu',
  components: {
    ToggleDarkMode,
    ToggleListMode,
    ToggleOrderMode,
    MetaInfo,
  },

  setup() {
  },
});
</script>
