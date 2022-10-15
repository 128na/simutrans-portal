<template>
  <q-layout view="hHh Lpr lFf" @click="popMenu.close">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="menu.toggle" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName}}
        </q-btn>
        <q-space />
        <toggle-dark-mode />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="menu.open" show-if-above bordered>
      <MypageMenu />
    </q-drawer>

    <q-page-container>
      <router-view v-if="auth.isInitialized" />
      <loading-page v-else />
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent } from 'vue';
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import { useAuthStore } from 'src/store/auth';
import { useColorStore } from 'src/store/color';
import { useMenuStore } from 'src/store/menu';
import { useAppInfo } from 'src/composables/appInfo';
import MypageMenu from 'src/components/Mypage/MypageMenu.vue';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import { usePopMenuStore } from 'src/store/popMenu';

export default defineComponent({
  name: 'MypageLayout',

  components: {
    MypageMenu,
    ToggleDarkMode,
    LoadingPage,
  },

  setup() {
    const { appName } = useAppInfo();
    useColorStore().setMypage();
    const menu = useMenuStore();

    const auth = useAuthStore();
    const popMenu = usePopMenuStore();

    return {
      auth,
      appName,
      menu,
      popMenu,
    };
  },
});
</script>
