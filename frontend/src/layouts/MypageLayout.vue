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
      <MypageMenu />
    </q-drawer>

    <q-page-container>
      <router-view v-if="authStore.isInitialized" />
      <q-page v-else class="flex flex-center">
        <loading-message />
      </q-page>
    </q-page-container>
  </q-layout>
</template>
<script>

import { defineComponent, ref } from 'vue';
import { setCssVar } from 'quasar';
import ToggleDarkMode from 'src/components/Common/ToggleDarkMode.vue';
import { useAuthStore } from 'src/store/auth';
import LoadingMessage from 'src/components/Common/LoadingMessage.vue';
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
    LoadingMessage,
  },

  setup() {
    const { appName } = appInfo();
    const leftDrawerOpen = ref(false);

    const authStore = useAuthStore();
    setCssVar('primary', 'hsl(132, 82%, 31%)');

    return {
      authStore,
      appName,
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
    };
  },
});
</script>
