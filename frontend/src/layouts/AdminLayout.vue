<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="menu.toggle" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName}}
        </q-btn>
        <q-space />
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="menu.open" show-if-above bordered>
      <AdminMenu />
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent } from 'vue';
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import { useColorStore } from 'src/store/color';
import { useMenuStore } from 'src/store/menu';
import { appInfo } from 'src/composables/appInfo';
import AdminMenu from 'src/components/Admin/AdminMenu.vue';

export default defineComponent({
  name: 'AdminLayout',
  props: {
    menuOpen: {
      type: Boolean,
      required: true,
    },
  },

  components: {
    AdminMenu,
    ToggleDarkMode,
  },

  setup() {
    const { appName } = appInfo();

    useColorStore().setAdmin();
    const menu = useMenuStore();

    return {
      appName,
      menu,
    };
  },
});
</script>
