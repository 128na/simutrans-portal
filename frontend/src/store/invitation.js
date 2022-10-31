import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useQuasar } from 'quasar';
import { useNotify } from 'src/composables/notify';

export const useInvitationStore = defineStore('invitation', () => {
  const invites = ref(null);
  const ready = computed(() => !!invites.value);
  const hasInvites = computed(() => !!invites.value?.length);

  const api = useMypageApi();
  const $q = useQuasar();
  const notify = useNotify();
  const { errorMessage, errorHandlerStrict } = useErrorHandler();
  const fetch = async () => {
    try {
      const res = await api.fetchInvites();
      invites.value = res.data.data;
    } catch (err) {
      errorHandlerStrict(err, '招待履歴の取得に失敗しました');
    }
  };

  const regenerate = async () => {
    try {
      $q.loading.show();
      const res = await api.updateInvitationCode();
      notify.success('招待コードを発行しました');
      return res.data.data;
    } catch (err) {
      return errorHandlerStrict(err, '招待コードの発行に失敗しました');
    } finally {
      $q.loading.hide();
    }
  };

  const revoke = async () => {
    try {
      $q.loading.show();
      const res = await api.deleteInvitationCode();
      return res.data.data;
    } catch (err) {
      notify.success('招待コードを削除しました');
      return errorHandlerStrict(err, '招待コードの削除に失敗しました');
    } finally {
      $q.loading.hide();
    }
  };

  return {
    fetch,
    ready,
    regenerate,
    revoke,
    errorMessage,
    invites,
    hasInvites,
  };
});
