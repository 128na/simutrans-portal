import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(undefined);

  const isInitialized = computed(() => user.value !== undefined);
  const isLoggedIn = computed(() => !!user.value);
  const isVerified = computed(() => isLoggedIn.value && user.value.verified);
  const isAdmin = computed(() => isLoggedIn.value && user.value.admin);

  const initialized = () => {
    user.value = null;
  };
  const loggedin = (loginUser) => {
    user.value = loginUser;
  };
  const logout = () => {
    user.value = null;
  };

  return {
    isInitialized,
    initialized,
    user,
    isLoggedIn,
    isVerified,
    isAdmin,
    loggedin,
    logout,
  };
});
