import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

export const useInvitationStore = defineStore('invitation', () => {
  const invites = ref(null);
  const ready = computed(() => !!invites.value);
  const hasInvites = computed(() => !!invites.value?.length);

  const api = useMypageApi();
  const handler = useApiHandler();
  const fetch = async () => {
    try {
      const res = await handler.handle({
        doRequest: () => api.fetchInvites(),
        failedMessage: '招待履歴の取得に失敗しました',
      });
      invites.value = res.data.data;
    } catch {
      // do nothing.
    }
  };

  const handlerCode = useApiHandler();
  const regenerate = async () => {
    const res = await handlerCode.handleWithLoading({
      doRequest: () => api.updateInvitationCode(),
      successMessage: '招待コードを発行しました',
      failedMessage: '招待コードの発行に失敗しました',
    });
    return res.data.data;
  };

  const revoke = async () => {
    const res = await handlerCode.handleWithLoading({
      doRequest: () => api.deleteInvitationCode(),
      successMessage: '招待コードを削除しました',
      failedMessage: '招待コードの削除に失敗しました',
    });
    return res.data.data;
  };

  return {
    fetch,
    ready,
    regenerate,
    revoke,
    handlerCode,
    invites,
    hasInvites,
  };
});
