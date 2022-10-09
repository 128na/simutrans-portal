<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="$emit('toggleMenu')" />
        <q-btn flat dense no-caps size="lg" :to="{ name: 'top' }">
          {{ appName}}
        </q-btn>
        <q-space />
        <ToggleListMode v-model="listMode" class="q-mr-md" />
        <ToggleDarkMode />
      </q-toolbar>
    </q-header>

    <q-drawer :model-value="menuOpen" show-if-above bordered @update:model-value="$emit('toggleMenu')">
      <FrontMenu />
    </q-drawer>

    <q-page-container>
      <router-view :listMode="listMode" />
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent, ref } from 'vue';
import { setCssVar } from 'quasar';
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import ToggleListMode from 'src/components/Common/ToggleListMode.vue';
import { appInfo } from '../composables/appInfo';
import FrontMenu from '../components/Front/FrontMenu.vue';

export default defineComponent({
  name: 'FrontLayout',
  props: {
    menuOpen: {
      type: Boolean,
      required: true,
    },
  },
  components: {
    FrontMenu,
    ToggleDarkMode,
    ToggleListMode,
  },

  setup() {
    const { appName } = appInfo();
    setCssVar('primary', 'hsl(211, 82%, 54%)');

    const listMode = ref('list');

    return {
      appName,
      listMode,
    };
  },
});
</script>
