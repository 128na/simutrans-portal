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
import { setCssVar } from 'quasar';
import { appInfo } from '../composables/appInfo';
import FrontMenu from '../components/Front/FrontMenu.vue';

export default defineComponent({
  name: 'FrontLayout',

  components: {
    FrontMenu,
  },

  setup() {
    const { appName, appVersion } = appInfo();
    const leftDrawerOpen = ref(false);
    setCssVar('primary', 'hsl(211, 82%, 54%)');

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
