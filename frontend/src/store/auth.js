import { defineStore } from 'pinia';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { useAppInfo } from 'src/composables/appInfo';
import { useNotify } from 'src/composables/notify';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(undefined);

  const isInitialized = computed(() => user.value !== undefined);
  const isLoggedIn = computed(() => !!user.value);
  const isVerified = computed(() => isLoggedIn.value && user.value.verified);
  const isAdmin = computed(() => isLoggedIn.value && user.value.admin);

  const setUser = (loginUser) => {
    user.value = loginUser;
  };

  const router = useRouter();
  const route = useRoute();
  const notify = useNotify();
  const api = useMypageApi();
  const handler = useApiHandler();
  const info = useAppInfo();
  const checkLoggedIn = async () => {
    try {
      const res = await api.fetchUser();
      user.value = res.data.data || null;
    } catch {
      // do nothing
    }
  };
  const attemptLogin = async (params) => {
    try {
      const res = await handler.handleWithValidate(() => api.postLogin(params));
      user.value = res.data.data;
      notify.success('ログインしました');
      router.push(route.query.redirect || { name: 'mypage' });
    } catch {
      // do nothing
    }
  };
  const attemptLogout = async () => {
    try {
      await api.postLogout();
    } finally {
      window.location.href = info.appUrl;
    }
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
    checkLoggedIn,
    attemptLogin,
    attemptLogout,
    validateAuth,
    setUser,
    handler,
  };
});
