export const useAppInfo = () => ({
  appName: process.env.APP_NAME,
  appVersion: process.env.APP_VERSION,
  appUrl: process.env.FRONTEND_URL,
  backendUrl: process.env.BACKEND_URL,
});
