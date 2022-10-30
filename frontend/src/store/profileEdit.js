import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useQuasar } from 'quasar';

export const useProfileEditStore = defineStore('profileEdit', () => {
  // article
  const user = ref(null);
  const setUser = (a) => {
    user.value = JSON.parse(JSON.stringify(a));
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

      return res.data.data;
    } catch (err) {
      return errorHandlerStrict(err);
    } finally {
      $q.loading.hide();
    }
  };

  const ready = computed(() => !!user.value);

  return {
    user,
    ready,
    setUser,
    updateUser,
    errorMessage,
  };
});
