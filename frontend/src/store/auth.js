import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter();
  const route = useRoute();
  const user = ref(undefined);

  const isInitialized = computed(() => user.value !== undefined);
  const isLoggedIn = computed(() => !!user.value);
  const isVerified = computed(() => isLoggedIn.value && user.value.verified);
  const isAdmin = computed(() => isLoggedIn.value && user.value.admin);

  const initialized = () => {
    user.value = null;
  };
  const login = (loginUser) => {
    user.value = loginUser;
  };
  const logout = () => {
    user.value = null;
  };

  const validateAuth = (currentRoute = route) => {
    console.log(currentRoute);
    if (currentRoute.meta.requiresAuth) {
      if (!isLoggedIn.value) {
        router.push({ replace: true, name: 'login', query: { redirect: currentRoute.href } });
        return false;
      }
    }
    if (currentRoute.meta.requiresVerified) {
      if (!isLoggedIn.value) {
        router.push({ replace: true, name: 'login', query: { redirect: currentRoute.href } });
        return false;
      }
      if (!isVerified.value) {
        router.push({ replace: true, name: 'requiresVerified' });
        return false;
      }
    }
    if (currentRoute.meta.requiresAdmin) {
      if (!isLoggedIn.value) {
        router.push({ replace: true, name: 'error', params: { status: 404 } });
        return false;
      }
      if (!isAdmin.value) {
        router.push({ replace: true, name: 'error', params: { status: 401 } });
        return false;
      }
    }
    return true;
  };

  return {
    isInitialized,
    user,
    isLoggedIn,
    isVerified,
    isAdmin,
    initialized,
    login,
    logout,
    validateAuth,
  };
});
