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
   * @param {()=>AxiosResponse<any>} doRequest
   * @param {string} defaultMessage
   * @returns {AxiosResponse<any>|null}
   */
  const handle = async (doRequest, defaultMessage = 'エラーが発生しました') => {
    try {
      loading.value = true;
      return await doRequest();
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
          notify.failedRetryable(defaultMessage, handle);
          throw error;
      }
    } finally {
      loading.value = false;
    }
  };

  /**
   * ローディング画面とエラーをハンドリングする
   * @param {()=>AxiosResponse<any>} doRequest
   * @param {string} defaultMessage
   * @returns {AxiosResponse<any>|null}
   */
  const handleWithLoading = async (doRequest, defaultMessage = 'エラーが発生しました') => {
    try {
      loading.value = true;
      $q.loading.show();
      return await doRequest();
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
          notify.failedRetryable(defaultMessage, handleWithLoading);
          throw error;
      }
    } finally {
      $q.loading.hide();
      loading.value = false;
    }
  };

  /**
   * ローディング画面とバリデーションエラーをハンドリングする
   * @param {()=>AxiosResponse<any>} doRequest
   * @param {string} defaultMessage
   * @returns {AxiosResponse<any>|null}
   */
  const handleWithValidate = async (doRequest, defaultMessage = 'エラーが発生しました') => {
    try {
      loading.value = true;
      $q.loading.show();
      validationErrors.value = {};
      return await doRequest();
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
          notify.failedRetryable(defaultMessage, handleWithValidate);
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
