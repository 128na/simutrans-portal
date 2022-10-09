<template>
  <router-view :menuOpen=menuOpen @toggleMenu="toggleMenu" />
</template>
<script>
import { defineComponent, ref } from 'vue';
import { useMypageApi } from './composables/api';
import { useAuthStore } from './store/auth';

export default defineComponent({
  name: 'App',
  setup() {
    const menuOpen = ref(false);
    const { fetchUser } = useMypageApi();
    const store = useAuthStore();
    const checkLogin = async () => {
      try {
        const res = await fetchUser();
        if (res.status === 200 && res.data) {
          return store.loggedin(res.data.data);
        }
      } catch (error) {
        // do nothing
      }
      return store.initialized();
    };
    checkLogin();

    return {
      store,
      menuOpen,
      toggleMenu() {
        menuOpen.value = !menuOpen.value;
      },
    };
  },
});
</script>
