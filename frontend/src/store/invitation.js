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
      await handler.handle({
        doRequest: () => api.fetchInvites(),
        done: (res) => {
          invites.value = res.data.data;
        },
        failedMessage: '招待履歴の取得に失敗しました',
      });
    } catch {
      // do nothing.
    }
  };

  const handlerCode = useApiHandler();
  const regenerate = async () => handlerCode.handleWithLoading({
    doRequest: () => api.updateInvitationCode(),
    done: (res) => res.data.data,
    successMessage: '招待コードを発行しました',
    failedMessage: '招待コードの発行に失敗しました',
  });

  const revoke = async () => handlerCode.handleWithLoading({
    doRequest: () => api.deleteInvitationCode(),
    done: (res) => res.data.data,
    successMessage: '招待コードを削除しました',
    failedMessage: '招待コードの削除に失敗しました',
  });

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
