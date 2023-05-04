import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

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
      done: () => {},
      successMessage: '保存しました',
    });

    return res.data.data;
  };

  onBeforeRouteLeave((to, from, next) => {
    // eslint-disable-next-line no-alert
    if (isModified(user.value) && !window.confirm('保存せずに移動しますか？')) {
      next(false);
    } else {
      next();
    }
  });
  onBeforeRouteUpdate((to, from, next) => {
    // eslint-disable-next-line no-alert
    if (isModified(user.value) && !window.confirm('保存せずに移動しますか？')) {
      next(false);
    } else {
      next();
    }
  });

  const ready = computed(() => !!user.value);
  const vali = (key) => handler.getValidationErrorByKey(key);

  return {
    user,
    ready,
    setUser,
    updateUser,
    handler,
    vali,
  };
});
