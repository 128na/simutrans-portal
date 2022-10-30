import { defineStore } from 'pinia';
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { BULK_ZIP_RETRY_INTERVAL, BULK_ZIP_RETRY_LIMIT } from 'src/const';
import { ref } from 'vue';

export const useBulkZipStore = defineStore('bulkZip', () => {
  const loading = ref(false);
  const retry = ref(0);
  const generated = ref(null);
  const { fetchUserBulkZip } = useMypageApi();
  const { errorHandlerStrict } = useErrorHandler();

  const successed = (data) => {
    loading.value = false;
    retry.value = 0;
    generated.value = data;
  };
  const failed = (error) => {
    loading.value = false;
    retry.value = 0;
    generated.value = null;
    return errorHandlerStrict(error, 'アーカイブ取得に失敗しました');
  };
  const wip = () => {
    retry.value += 1;
    // eslint-disable-next-line no-use-before-define
    return setTimeout(doCheck, BULK_ZIP_RETRY_INTERVAL);
  };
  const doCheck = async () => {
    try {
      const res = await fetchUserBulkZip();

      if (res.status === 200 && res.data.generated) {
        return successed(res.data);
      }
      if (retry.value < BULK_ZIP_RETRY_LIMIT) {
        return wip();
      }
      throw new Error('retry limit reached');
    } catch (error) {
      return failed(error);
    }
  };

  const fetch = async () => {
    loading.value = true;
    retry.value = 0;
    generated.value = null;
    doCheck();
  };

  const cancel = () => {
    // todo cancel request;
    loading.value = false;
    retry.value = 0;
  };
  return {
    loading,
    generated,
    fetch,
    cancel,
  };
});
