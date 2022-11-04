import axios from 'axios';

const listenWindowError = () => {
  window.addEventListener('error', async (event) => {
    try {
      const params = {
        l: window.location.href,
        n: event.error.name,
        m: event.error.message,
        s: event.error?.stack,
      };
      axios.post('/api/v3/logger', params);
    } catch {
      // do nothing.
    }
  });
};
const listenVueError = (app) => {
  app.config.errorHandler = async (error, vm, info) => {
    try {
      const params = {
        l: window.location.href,
        n: error.name,
        m: error.message,
        s: error?.stack,
        i: info,
      };
      axios.post('/api/v3/logger', params);
    } catch {
      // do nothing.
    }
  };
};

export default ({ app }) => {
  listenWindowError();
  listenVueError(app);
};
