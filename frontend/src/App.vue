<template>
  <router-view />
</template>
<script>
import { defineComponent } from 'vue';
import { useAuthStore } from 'src/store/auth';
import { createPinia, setActivePinia } from 'pinia';

export default defineComponent({
  name: 'App',
  setup() {
    const pinia = createPinia();
    setActivePinia(pinia);

    const auth = useAuthStore();
    auth.checkLoggedIn();
  },
});
</script>
