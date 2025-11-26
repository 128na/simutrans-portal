import { createRoot } from "react-dom/client";
import { Analytics } from "../../features/analytics/Analytics";
import { ErrorBoundary } from "../../components/ErrorBoundary";

const app = document.getElementById("app-analytics");
const articles = JSON.parse(
  document.getElementById("data-articles")?.textContent || "[]"
);
if (app) {
  const App = () => {
    return <Analytics articles={articles} />;
  };

  createRoot(app).render(
    <ErrorBoundary name="AnalyticsPage">
      <App />
    </ErrorBoundary>
  );
}
