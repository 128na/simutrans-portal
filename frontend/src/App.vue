<template>
  <router-view />
</template>
<script>
import { defineComponent } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';

export default defineComponent({
  name: 'App',
  setup() {
    const { fetchUser } = useMypageApi();
    const store = useAuthStore();
    const checkLogin = async () => {
      try {
        const res = await fetchUser();
        if (res.status === 200 && res.data?.data) {
          return store.login(res.data.data);
        }
      } catch (error) {
        // do nothing
      }
      return store.initialized();
    };
    checkLogin();

    return {
      store,
    };
  },
});
</script>
