<template>
  <q-layout view="hHh LpR lFr">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="menu.toggle" />
        <q-btn flat dense no-caps size="lg" href="/">
          {{ appName }}
        </q-btn>
        <q-space />
        <q-btn flat dense round icon="menu" @click="menuRight.toggle" />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="menu.open" bordered>
      <FrontMenu />
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <q-drawer side="right" v-model="menuRight.open" bordered>
      <FrontRightMenu />
    </q-drawer>

  </q-layout>
</template>
<script>

import { defineComponent } from 'vue';
import { useMenuStore, useMenuRightStore } from 'src/store/menu';
import { useColor } from 'src/composables/color';
import { useAppInfo } from 'src/composables/appInfo';
import FrontMenu from 'src/components/Front/FrontMenu.vue';
import FrontRightMenu from 'src/components/Front/FrontRightMenu.vue';

export default defineComponent({
  name: 'FrontLayout',
  components: {
    FrontMenu,
    FrontRightMenu,
  },

  setup() {
    const { appName } = useAppInfo();
    useColor().setFront();

    const menu = useMenuStore();
    const menuRight = useMenuRightStore();
    return {
      appName,
      menu,
      menuRight,
    };
  },
});
</script>
