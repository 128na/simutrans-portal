import { useNotify } from 'src/composables/notify';
import { useRouter } from 'vue-router';
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar';
// eslint-disable-next-line no-unused-vars
import { AxiosResponse } from 'axios';

export const useApiHandler = () => {
  const validationErrors = ref({});
  const loading = ref(false);
  const notify = useNotify();
  const router = useRouter();
  const $q = useQuasar();

  const validationErrorMessage = computed(() => Object.values(validationErrors.value).map((m) => m.join('、')).join('\n'));

  /**
   * エラーをハンドリングする
   * @param {{doRequest:()=>AxiosResponse<any>, successMessage:string, failedMessage:string, retryable:boolean}}
   * @returns {AxiosResponse<any>|null}
   */
  const handle = async ({
    doRequest, successMessage = null, failedMessage = 'エラーが発生しました', retryable = true,
  }) => {
    try {
      loading.value = true;
      const res = await doRequest();
      if (successMessage) {
        notify.success(successMessage);
      }
      return res;
    } catch (error) {
      switch (error.response.status) {
        case 401:
          router.replace({ name: 'login' });
          throw error;
        case 403:
        case 404:
        case 419:
        case 429:
          router.replace({ name: 'error', params: { status: error.response.status } });
          throw error;
        default:
          if (retryable) {
            notify.failedRetryable(failedMessage, handle);
          } else {
            notify.failed(failedMessage);
          }
          throw error;
      }
    } finally {
      loading.value = false;
    }
  };

  /**
   * ローディング画面とエラーをハンドリングする
   * @param {{doRequest:()=>AxiosResponse<any>, successMessage:string, failedMessage:string, retryable:boolean}}
   * @returns {AxiosResponse<any>|null}
   */
  const handleWithLoading = async ({
    doRequest, successMessage = null, failedMessage = 'エラーが発生しました', retryable = true,
  }) => {
    try {
      loading.value = true;
      $q.loading.show();
      const res = await doRequest();
      if (successMessage) {
        notify.success(successMessage);
      }
      return res;
    } catch (error) {
      switch (error.response.status) {
        case 401:
          router.replace({ name: 'login' });
          throw error;
        case 403:
        case 404:
        case 419:
        case 429:
          router.replace({ name: 'error', params: { status: error.response.status } });
          throw error;
        default:
          if (retryable) {
            notify.failedRetryable(failedMessage, handleWithLoading);
          } else {
            notify.failed(failedMessage);
          }
          throw error;
      }
    } finally {
      $q.loading.hide();
      loading.value = false;
    }
  };

  /**
   * ローディング画面とバリデーションエラーをハンドリングする
   * @param {{doRequest:()=>AxiosResponse<any>, successMessage:string, failedMessage:string, retryable:boolean}}
   * @returns {AxiosResponse<any>|null}
   */
  const handleWithValidate = async ({
    doRequest, successMessage = null, failedMessage = 'エラーが発生しました', retryable = true,
  }) => {
    try {
      loading.value = true;
      $q.loading.show();
      validationErrors.value = {};
      const res = await doRequest();
      if (successMessage) {
        notify.success(successMessage);
      }
      return res;
    } catch (error) {
      switch (error.response.status) {
        case 401:
          router.replace({ name: 'login' });
          throw error;
        case 403:
        case 404:
        case 419:
        case 429:
          router.replace({ name: 'error', params: { status: error.response.status } });
          throw error;
        case 422:
          validationErrors.value = error.response.data.errors;
          throw error;
        default:
          if (retryable) {
            notify.failedRetryable(failedMessage, handleWithValidate);
          } else {
            notify.failed(failedMessage);
          }
          throw error;
      }
    } finally {
      $q.loading.hide();
      loading.value = false;
    }
  };

  return {
    validationErrors,
    validationErrorMessage,
    loading,
    handle,
    handleWithLoading,
    handleWithValidate,
  };
};
