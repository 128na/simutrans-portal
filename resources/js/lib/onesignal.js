/* eslint no-undef: 0, no-console: 0 */
window.OneSignalDeferred = window.OneSignalDeferred || [];
OneSignalDeferred.push(async function (OneSignal) {
  const appId = import.meta.env.VITE_ONESIGNAL_APP_ID;

  if (!appId) {
    console.warn("OneSignal: VITE_ONESIGNAL_APP_ID is not set");
    return;
  }

  try {
    await OneSignal.init({
      appId,
      notifyButton: { enable: true },
      allowLocalhostAsSecureOrigin: import.meta.env.DEV,
    });
  } catch (error) {
    console.error("OneSignal initialization failed:", error);
  }
});
