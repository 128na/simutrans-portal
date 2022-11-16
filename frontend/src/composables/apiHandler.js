import { useNotify } from 'src/composables/notify';
import { useRouter } from 'vue-router';
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar';
// eslint-disable-next-line no-unused-vars
import { AxiosResponse } from 'axios';
import { useAuthStore } from 'src/store/auth';

export const useApiHandler = () => {
  const validationErrors = ref({});
  const loading = ref(false);
  const notify = useNotify();
  const router = useRouter();
  const $q = useQuasar();
  const auth = useAuthStore();

  const validationErrorMessage = computed(() => Object.values(validationErrors.value).map((m) => m.join('、')).join('\n'));
  const getValidationErrorByKey = (key) => validationErrors.value?.[key]?.join('、');
  const hasValidationErrorByKey = (key) => !!validationErrors.value?.[key];

  /**
   * エラーをハンドリングする
   * @param {{doRequest:()=>AxiosResponse<any>, done:(AxiosResponse:res)=>void, successMessage:string, failedMessage:string, retryable:boolean, autoRetry:number ,retryCount:number}}
   * @returns {AxiosResponse<any>|null}
   */
  const handle = async ({
    doRequest, done, successMessage = null, failedMessage = 'エラーが発生しました', retryable = true, autoRetry = 3, retryCount = 0,
  }) => {
    try {
      loading.value = true;
      const res = await doRequest();
      if (successMessage) {
        notify.success(successMessage);
      }
      return done ? await done(res) : res;
    } catch (error) {
      switch (error.response.status) {
        case 401:
        case 419:
          notify.info('ログイン期限が切れました。再度ログインしてください');
          auth.setUser(null);
          router.push({ name: 'login' });
          throw error;
        case 403:
        case 404:
        case 429:
          router.replace({ name: 'error', params: { status: error.response.status } });
          throw error;
        case 422:
          validationErrors.value = error.response.data.errors;
          notify.failed(validationErrorMessage.value);
          throw error;
        case 500:
        case 503:
          if (autoRetry && retryCount < autoRetry) {
            return setTimeout(() => {
              handle({
                doRequest, done, successMessage, failedMessage, retryable, autoRetry, retryCount: retryCount + 1,
              });
            }, 1000);
          }
          if (retryable) {
            notify.failedRetryable(failedMessage, () => handle({
              doRequest, done, successMessage, failedMessage, retryable,
            }));
          } else {
            notify.failed(failedMessage);
          }
          throw error;
        default:
          if (retryable) {
            notify.failedRetryable(failedMessage, () => handle({
              doRequest, done, successMessage, failedMessage, retryable,
            }));
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
   * @param {{doRequest:()=>AxiosResponse<any>, done:(AxiosResponse:res)=>void, successMessage:string, failedMessage:string, retryable:boolean, autoRetry:number ,retryCount:number}}
   * @returns {AxiosResponse<any>|null}
   */
  const handleWithLoading = async ({
    doRequest, done, successMessage = null, failedMessage = 'エラーが発生しました', retryable = true, autoRetry = 3, retryCount = 0,
  }) => {
    try {
      loading.value = true;
      $q.loading.show();
      const res = await doRequest();
      if (successMessage) {
        notify.success(successMessage);
      }
      return done ? await done(res) : res;
    } catch (error) {
      switch (error.response.status) {
        case 401:
        case 419:
          notify.info('ログイン期限が切れました。再度ログインしてください');
          auth.setUser(null);
          router.push({ name: 'login' });
          throw error;
        case 403:
        case 404:
        case 429:
          router.replace({ name: 'error', params: { status: error.response.status } });
          throw error;
        case 422:
          validationErrors.value = error.response.data.errors;
          notify.failed(validationErrorMessage.value);
          throw error;
        case 500:
        case 503:
          if (autoRetry && retryCount < autoRetry) {
            return setTimeout(() => {
              handle({
                doRequest, done, successMessage, failedMessage, retryable, autoRetry, retryCount: retryCount + 1,
              });
            }, 1000);
          }
          if (retryable) {
            notify.failedRetryable(failedMessage, () => handleWithLoading({
              doRequest, done, successMessage, failedMessage, retryable,
            }));
          } else {
            notify.failed(failedMessage);
          }
          throw error;
        default:
          if (retryable) {
            notify.failedRetryable(failedMessage, () => handleWithLoading({
              doRequest, done, successMessage, failedMessage, retryable,
            }));
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
   * @param {{doRequest:()=>AxiosResponse<any>, done:(AxiosResponse:res)=>void, successMessage:string, failedMessage:string, retryable:boolean, autoRetry:number ,retryCount:number}}
   * @returns {AxiosResponse<any>|null}
   */
  const handleWithValidate = async ({
    doRequest, done, successMessage = null, failedMessage = 'エラーが発生しました', retryable = true, autoRetry = 3, retryCount = 0,
  }) => {
    try {
      loading.value = true;
      $q.loading.show();
      validationErrors.value = {};
      const res = await doRequest();
      if (successMessage) {
        notify.success(successMessage);
      }
      return done ? await done(res) : res;
    } catch (error) {
      switch (error.response.status) {
        case 401:
        case 419:
          notify.info('ログイン期限が切れました。再度ログインしてください');
          auth.setUser(null);
          router.push({ name: 'login' });
          throw error;
        case 403:
        case 404:
        case 429:
          router.replace({ name: 'error', params: { status: error.response.status } });
          throw error;
        case 422:
          validationErrors.value = error.response.data.errors;
          throw error;
        case 500:
        case 503:
          if (autoRetry && retryCount < autoRetry) {
            return setTimeout(() => {
              handle({
                doRequest, done, successMessage, failedMessage, retryable, autoRetry, retryCount: retryCount + 1,
              });
            }, 1000);
          }
          if (retryable) {
            notify.failedRetryable(failedMessage, () => handleWithLoading({
              doRequest, done, successMessage, failedMessage, retryable,
            }));
          } else {
            notify.failed(failedMessage);
          }
          throw error;
        default:
          if (retryable) {
            notify.failedRetryable(failedMessage, () => handleWithValidate({
              doRequest, done, successMessage, failedMessage, retryable,
            }));
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

  const clearValidationErrors = () => {
    validationErrors.value = {};
  };

  return {
    validationErrors,
    validationErrorMessage,
    getValidationErrorByKey,
    hasValidationErrorByKey,
    clearValidationErrors,
    loading,
    handle,
    handleWithLoading,
    handleWithValidate,
  };
};
