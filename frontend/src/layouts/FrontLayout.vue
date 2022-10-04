<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="$emit('toggleMenu')" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName}}
        </q-btn>
        <q-space />
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer :model-value="menuOpen" show-if-above bordered @update:model-value="$emit('toggleMenu')">
      <FrontMenu />
    </q-drawer>

    <q-page-container>
      <router-view :cachedArticles="state.cachedArticles" @addCache="handleAddCache" @addCaches="handleAddCaches" />
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent } from 'vue';
import { setCssVar } from 'quasar';
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import { cachedArticles } from 'src/composables/cachedArticles';
import { appInfo } from '../composables/appInfo';
import FrontMenu from '../components/Front/FrontMenu.vue';

export default defineComponent({
  name: 'FrontLayout',
  props: {
    menuOpen: {
      type: Boolean,
      required: true,
    },
  },
  components: {
    FrontMenu,
    ToggleDarkMode,
  },

  setup() {
    const { appName } = appInfo();
    setCssVar('primary', 'hsl(211, 82%, 54%)');

    const { state, handleAddCache, handleAddCaches } = cachedArticles();

    return {
      appName,
      state,
      handleAddCache,
      handleAddCaches,
    };
  },
});
</script>
