import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

// 変更検知用

export const useProfileEditStore = defineStore('profileEdit', () => {
  // article
  const user = ref(null);
  const setUser = (a) => {
    user.value = JSON.parse(JSON.stringify(a));
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
