<template>
  <router-view />
</template>
<script>
import { defineComponent } from 'vue';
import { useAuthStore } from 'src/store/auth';

export default defineComponent({
  name: 'App',
  setup() {
    const auth = useAuthStore();
    auth.checkLoggedIn();
  },
});
</script>
