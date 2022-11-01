import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';
import { onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useQuasar } from 'quasar';

const unloadListener = (event) => {
  event.preventDefault();
  event.returnValue = '';
};
// 変更検知用
let original = null;

const isModified = (val) => {
  const current = JSON.stringify(val);
  return original !== current;
};

export const useProfileEditStore = defineStore('profileEdit', () => {
  // article
  const user = ref(null);
  const setUser = (a) => {
    user.value = JSON.parse(JSON.stringify(a));
    original = JSON.stringify(a);
  };

  const notify = useNotify();
  const api = useMypageApi();
  const $q = useQuasar();
  const { errorMessage, errorHandlerStrict } = useErrorHandler();
  const updateUser = async () => {
    try {
      $q.loading.show();
      const res = await api.updateUser(user.value);
      notify.success('保存しました');
      window.removeEventListener('beforeunload', unloadListener);

      return res.data.data;
    } catch (err) {
      return errorHandlerStrict(err);
    } finally {
      $q.loading.hide();
    }
  };

  watch(user, (v) => {
    if (isModified(v)) {
      window.addEventListener('beforeunload', unloadListener);
    } else {
      window.removeEventListener('beforeunload', unloadListener);
    }
  }, { deep: true });

  onBeforeRouteLeave((to, from, next) => {
    // eslint-disable-next-line no-alert
    if (isModified(user.value) && !window.confirm('保存せずに移動しますか？')) {
      window.addEventListener('beforeunload', unloadListener);
      next(false);
    } else {
      window.removeEventListener('beforeunload', unloadListener);
      next();
    }
  });
  onBeforeRouteUpdate((to, from, next) => {
    // eslint-disable-next-line no-alert
    if (isModified(user.value) && !window.confirm('保存せずに移動しますか？')) {
      window.addEventListener('beforeunload', unloadListener);
      next(false);
    } else {
      window.removeEventListener('beforeunload', unloadListener);
      next();
    }
  });

  const ready = computed(() => !!user.value);

  return {
    user,
    ready,
    setUser,
    updateUser,
    errorMessage,
  };
});
