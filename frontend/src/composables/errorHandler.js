import { ref } from 'vue';

export const useErrorHandler = (router) => {
  const errorMessage = ref('');

  const setValidationErrorMessage = (errors) => {
    // eslint-disable-next-line no-console
    console.log({ errors });
  };
  const setErrorMessage = (error) => {
    errorMessage.value = error;
  };

  /**
   *  エラーだとページが成り立たなくなるようなケース用
   */
  const errorHandlerStrict = (error, defaultMessage = 'エラーが発生しました') => {
    const status = error.response.status || 0;

    switch (status) {
      case 422:
        return setValidationErrorMessage(error.response.data.errors);
      case 500: // 最近よくあるっぽい
        return setErrorMessage(defaultMessage);
      default:
        return router.push({ name: 'error', params: { status } });
    }
  };
  /**
   *  ページの一部など
   */
  const errorHandler = (error, defaultMessage = 'エラーが発生しました') => {
    const status = error.response.status || 0;

    switch (status) {
      case 404:
      case 429:
        return router.push({ name: 'error', params: { status } });
      default:
        return setErrorMessage(defaultMessage);
    }
  };

  return { errorMessage, errorHandler, errorHandlerStrict };
};
