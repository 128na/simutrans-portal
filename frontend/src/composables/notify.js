import { useQuasar } from 'quasar';

export const useNotify = () => {
  const $q = useQuasar();

  const success = (message) => $q.notify({
    type: 'positive',
    position: 'top',
    message,
  });
  const info = (message) => $q.notify({
    type: 'info',
    position: 'top',
    message,
  });
  const failed = (message) => $q.notify({
    type: 'negative',
    position: 'top',
    message,
    timeout: 0,
    actions: [
      { icon: 'close', color: 'white' },
    ],
  });
  return {
    success,
    info,
    failed,
  };
};
