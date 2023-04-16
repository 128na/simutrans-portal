<template>
  <q-layout view="hHh LpR lFr">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="menu.toggle" />
        <q-btn flat dense no-caps size="lg" href="/">
          {{ appName }}
        </q-btn>
        <q-space />
        <q-btn flat dense round icon="settings" @click="menuRight.toggle" />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="menu.open" show-if-above bordered>
      <MypageMenu />
    </q-drawer>

    <q-page-container>
      <router-view v-if="auth.isInitialized" />
      <loading-page v-else />
    </q-page-container>

    <q-drawer side="right" v-model="menuRight.open" bordered>
      <FrontRightMenu />
    </q-drawer>
  </q-layout>
</template>
<script>

import { defineComponent } from 'vue';
import { useAuthStore } from 'src/store/auth';
import { useColor } from 'src/composables/color';
import { useMenuRightStore, useMenuStore } from 'src/store/menu';
import { useAppInfo } from 'src/composables/appInfo';
import MypageMenu from 'src/components/Mypage/MypageMenu.vue';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import FrontRightMenu from 'src/components/Front/FrontRightMenu.vue';

export default defineComponent({
  name: 'MypageLayout',

  components: {
    MypageMenu,
    LoadingPage,
    FrontRightMenu,
  },

  setup() {
    const { appName } = useAppInfo();
    useColor().setMypage();
    const menu = useMenuStore();
    const menuRight = useMenuRightStore();

    const auth = useAuthStore();

    return {
      auth,
      appName,
      menu,
      menuRight,
    };
  },
});
</script>
