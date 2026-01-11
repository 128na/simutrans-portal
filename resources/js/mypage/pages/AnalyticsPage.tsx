import { createRoot } from "react-dom/client";
import { Analytics } from "../../features/analytics/Analytics";
import { AppWrapper } from "../../components/AppWrapper";

const app = document.getElementById("app-analytics");
const articles = JSON.parse(
  document.getElementById("data-articles")?.textContent || "[]"
);
if (app) {
  const App = () => {
    return <Analytics articles={articles} />;
  };

  createRoot(app).render(
    <AppWrapper boundaryName="AnalyticsPage">
      <App />
    </AppWrapper>
  );
}
