<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="toggleLeftDrawer" />

        <q-toolbar-title @click="toggleLeftDrawer">
          {{ appName}}
        </q-toolbar-title>

        <div>v{{ appVersion }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <FrontMenu />
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent, ref } from 'vue';
import { appInfo } from '../composables/appInfo';
import FrontMenu from '../components/menu/FrontMenu.vue';

export default defineComponent({
  name: 'MainLayout',

  components: {
    FrontMenu,
  },

  setup() {
    const { appName, appVersion } = appInfo();
    const leftDrawerOpen = ref(false);

    return {
      appName,
      appVersion,
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
    };
  },
});
</script>
