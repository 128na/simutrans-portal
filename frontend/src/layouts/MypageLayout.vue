<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="$emit('toggleMenu')" />

        <q-toolbar-title>
          {{ appName}}
        </q-toolbar-title>
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer :model-value="menuOpen" show-if-above bordered @update:model-value="$emit('toggleMenu')">
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
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import { appInfo } from '../composables/appInfo';
import MypageMenu from '../components/Mypage/MypageMenu.vue';

export default defineComponent({
  name: 'MypageLayout',

  props: {
    menuOpen: {
      type: Boolean,
      required: true,
    },
  },
  components: {
    MypageMenu,
    ToggleDarkMode,
  },

  setup() {
    const { appName } = appInfo();
    const leftDrawerOpen = ref(false);

    setCssVar('primary', 'hsl(132, 82%, 31%)');

    return {
      appName,
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
    };
  },
});
</script>
