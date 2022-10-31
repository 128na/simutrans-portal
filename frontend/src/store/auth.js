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
  const setUser = (loginUser) => {
    user.value = loginUser;
  };
  const logout = () => {
    user.value = null;
  };

  const validateAuth = () => {
    // 個別ルートハンドリング
    if (route.name === 'requiresVerified' && isVerified.value) {
      router.replace({ name: 'mypage' });
    }

    if (route.meta.requiresGuest && isLoggedIn.value) {
      router.replace({ name: 'mypage' });
    }
    if (route.meta.requiresAuth) {
      if (!isLoggedIn.value) {
        router.push({ replace: true, name: 'login', query: { redirect: route.href } });
        return false;
      }
    }
    if (route.meta.requiresVerified) {
      if (!isLoggedIn.value) {
        router.push({ replace: true, name: 'login', query: { redirect: route.href } });
        return false;
      }
      if (!isVerified.value) {
        router.push({ replace: true, name: 'requiresVerified' });
        return false;
      }
    }
    if (route.meta.requiresAdmin) {
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
    setUser,
  };
});
