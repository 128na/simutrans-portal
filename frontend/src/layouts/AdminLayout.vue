<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="menu.toggle" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName }}
        </q-btn>
        <q-space />
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="menu.open" show-if-above bordered>
      <AdminMenu />
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
import { useColor } from 'src/composables/color';
import { useMenuStore } from 'src/store/menu';
import { useAppInfo } from 'src/composables/appInfo';
import AdminMenu from 'src/components/Admin/AdminMenu.vue';
import { useAuthStore } from 'src/store/auth';
import LoadingPage from 'src/components/Common/LoadingPage.vue';

export default defineComponent({
  name: 'AdminLayout',

  components: {
    AdminMenu,
    ToggleDarkMode,
    LoadingPage,
  },

  setup() {
    const { appName } = useAppInfo();

    useColor().setAdmin();
    const menu = useMenuStore();
    const auth = useAuthStore();

    return {
      auth,
      appName,
      menu,
    };
  },
});
</script>
