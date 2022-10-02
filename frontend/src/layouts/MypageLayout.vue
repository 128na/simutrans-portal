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
      <MypageMenu />
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
import MypageMenu from '../components/Mypage/MypageMenu.vue';

export default defineComponent({
  name: 'MypageLayout',

  components: {
    MypageMenu,
  },

  setup() {
    const { appName, appVersion } = appInfo();
    const leftDrawerOpen = ref(false);

    setCssVar('primary', 'hsl(132, 82%, 31%)');

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
