<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="$emit('toggleMenu')" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName}}
        </q-btn>
        <q-space />
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer :model-value="menuOpen" show-if-above bordered @update:model-value="$emit('toggleMenu')">
      <AdminMenu />
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
import AdminMenu from '../components/Admin/AdminMenu.vue';

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
    const leftDrawerOpen = ref(false);

    setCssVar('primary', 'hsl(345, 82%, 35%)');

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
