<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="menu.toggle" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName}}
        </q-btn>
        <q-space />
        <ToggleListMode class="q-mr-md" />
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="menu.open" show-if-above bordered>
      <FrontMenu />
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent } from 'vue';
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import ToggleListMode from 'src/components/Common/ToggleListMode.vue';
import { useMenu } from 'src/store/menu';
import { useColor } from 'src/store/color';
import { appInfo } from '../composables/appInfo';
import FrontMenu from '../components/Front/FrontMenu.vue';

export default defineComponent({
  name: 'FrontLayout',
  components: {
    FrontMenu,
    ToggleDarkMode,
    ToggleListMode,
  },

  setup() {
    const { appName } = appInfo();
    useColor().setFront();

    const menu = useMenu();
    return {
      appName,
      menu,
    };
  },
});
</script>
