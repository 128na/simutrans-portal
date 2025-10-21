import { useEffect } from "react";
import { createRoot } from "react-dom/client";
import OneSignal from "react-onesignal";
const app = document.getElementById("app-one-signal");

if (app) {
  const appId = app.dataset.appId || ("" as string);

  const App = () => {
    useEffect(() => {
      OneSignal.init({ appId, subdomainName: "localhost" }).then(() => {
        OneSignal.Debug.setLogLevel("trace");
      });
    }, []);
    return <div>aaa</div>;
  };

  createRoot(app).render(<App />);
}
