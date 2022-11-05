import { defineStore } from 'pinia';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { BULK_ZIP_RETRY_INTERVAL, BULK_ZIP_RETRY_LIMIT } from 'src/const';
import { ref } from 'vue';

export const useBulkZipStore = defineStore('bulkZip', () => {
  const inProgress = ref(false);
  const retry = ref(0);
  const generated = ref(null);
  const { fetchUserBulkZip } = useMypageApi();

  const successed = (data) => {
    inProgress.value = false;
    retry.value = 0;
    generated.value = data;
  };
  const failed = () => {
    inProgress.value = false;
    retry.value = 0;
    generated.value = null;
  };
  const doRetry = () => {
    retry.value += 1;
    // eslint-disable-next-line no-use-before-define
    return setTimeout(doCheck, BULK_ZIP_RETRY_INTERVAL);
  };
  const handler = useApiHandler();
  const doCheck = async () => {
    try {
      return await handler.handle({
        doRequest: () => fetchUserBulkZip(),
        done: (res) => {
          if (res.data.generated) {
            return successed(res.data);
          }
          if (retry.value < BULK_ZIP_RETRY_LIMIT) {
            return doRetry();
          }
          throw new Error('retry limit reached');
        },
      });
    } catch {
      return failed();
    }
  };

  const fetch = async () => {
    inProgress.value = true;
    retry.value = 0;
    generated.value = null;
    doCheck();
  };

  const cancel = () => {
    // todo cancel request;
    inProgress.value = false;
    retry.value = 0;
  };
  return {
    inProgress,
    generated,
    fetch,
    cancel,
  };
});
