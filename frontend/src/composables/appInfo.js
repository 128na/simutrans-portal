export function appInfo() {
  return {
    appName: process.env.APP_NAME,
    appVersion: process.env.APP_VERSION,
    appUrl: process.env.APP_URL,
    backendUrl: process.env.BACKEND_URL,
  };
}
