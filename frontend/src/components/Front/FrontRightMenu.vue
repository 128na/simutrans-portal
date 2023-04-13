<template>
  <q-list padding>
    <q-item-label header>一覧スタイル</q-item-label>
    <q-item>
      <ToggleListMode />
    </q-item>
    <q-item-label header>色</q-item-label>
    <q-item>
      <ToggleDarkMode />
    </q-item>
  </q-list>
</template>
<script>

import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import ToggleListMode from 'src/components/Common/ToggleListMode.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'FrontRightMenu',
  components: {
    ToggleDarkMode,
    ToggleListMode,
  },

  setup() {
  },
});
</script>
