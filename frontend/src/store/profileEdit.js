import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';
import { onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

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

  const api = useMypageApi();
  const handler = useApiHandler();
  const updateUser = async () => {
    const res = await handler.handleWithValidate({
      doRequest: () => api.updateUser(user.value),
      successMessage: '保存しました',
    });
    window.removeEventListener('beforeunload', unloadListener);

    return res.data.data;
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
    handler,
  };
});
