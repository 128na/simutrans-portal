/* eslint no-undef: 0 */
import { env, hasOneSignal } from "./env";

if (hasOneSignal()) {
  window.OneSignalDeferred = window.OneSignalDeferred || [];
  OneSignalDeferred.push(async function (OneSignal) {
    await OneSignal.init({
      appId: env.onesignalAppId,
      notifyButton: { enable: true },
      allowLocalhostAsSecureOrigin: true,
    });
  });
}
