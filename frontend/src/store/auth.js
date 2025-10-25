import { defineStore } from 'pinia';
import { Exception } from 'sass';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { useNotify } from 'src/composables/notify';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(undefined);
  const requireTFA = ref(false);

  const isInitialized = computed(() => user.value !== undefined);
  const isLoggedIn = computed(() => !!user.value);
  const isVerified = computed(() => isLoggedIn.value && user.value.verified);
  const isAdmin = computed(() => isLoggedIn.value && user.value.admin);
  const notify = useNotify();

  const setUser = (loginUser) => {
    user.value = loginUser;
  };

  const api = useMypageApi();
  const initializeCsrf = async () => {
    await api.getCsrf();
  };

  const router = useRouter();
  const route = useRoute();
  const handler = useApiHandler();
  const checkLoggedIn = async () => {
    try {
      const res = await api.fetchUser();
      user.value = res.data.data || null;
    } catch {
      user.value = null;
    }
  };
  const attemptLogin = async (params) => {
    try {
      await handler.handleWithValidate({
        doRequest: () => api.postLogin(params),
        done: (res) => {
          if (res.data.two_factor) {
            requireTFA.value = true;
            return;
          }
          checkLoggedIn()
            .then(() => {
              notify.success('ログインしました');
              router.push(route.query.redirect || { name: 'mypage' });
            });
        },
      });
    } catch {
      // do nothing
    }
  };
  const attemptLogout = async () => {
    try {
      await api.postLogout();
      user.value = null;
      requireTFA.value = false;
    } finally {
      window.location.href = '/';
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
        router.push({ replace: true, name: 'error', params: { status: 404 } });
        return false;
      }
    }
    return true;
  };
  const isOwnedArticle = (article) => Number(article.user.id) === user.value?.id;

  const setupTFAQrCode = async () => {
    await api.twoFactorAuthentication();
    const res = await api.twoFactorQrCode();
    if (res.data.svg.startsWith('<svg')) {
      return res.data.svg;
    }
    throw new Exception('invalid svg response');
  };
  const confirmTFACode = async (code) => {
    try {
      return await handler.handleWithValidate({
        doRequest: () => api.confirmedTwoFactorAuthentication({ code }),
        done: async () => {
          user.value.two_factor = true;
          const res = await api.twoFactorRecoveryCodes();

          return res.data.join('\n');
        },
        successMessage: '設定が完了しました',
      });
    } catch {
      // バリデーションエラーでもそれっぽい値がセットされる謎
      await api.deleteTwoFactorAuthentication();
    }
    return null;
  };
  const attemptTFA = async (params) => {
    try {
      await handler.handleWithValidate({
        doRequest: () => api.challengeTwoFactorAuthentication(params),
        done: () => {
          checkLoggedIn()
            .then(() => router.push(route.query.redirect || { name: 'mypage' }));
        },
        successMessage: 'ログインしました',
      });
    } catch {
      // do nothing
    }
  };
  const deleteTFA = async () => {
    try {
      await handler.handle({
        doRequest: () => api.deleteTwoFactorAuthentication(),
        done: () => {
          user.value.two_factor = false;
        },
        successMessage: '無効化しました',
      });
    } catch {
      // do nothing
    }
  };

  return {
    isInitialized,
    user,
    isLoggedIn,
    isVerified,
    isAdmin,
    initializeCsrf,
    checkLoggedIn,
    attemptLogin,
    attemptLogout,
    validateAuth,
    setUser,
    handler,
    isOwnedArticle,
    setupTFAQrCode,
    confirmTFACode,
    attemptTFA,
    deleteTFA,
    requireTFA,
  };
});
