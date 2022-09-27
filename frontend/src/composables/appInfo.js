export default function appInfo() {
  return {
    appName: process.env.APP_NAME,
    appVersion: process.env.APP_VERSION,
  };
}
