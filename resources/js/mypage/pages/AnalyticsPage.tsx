import { createRoot } from "react-dom/client";
import { Analytics } from "../../features/analytics/Analytics";
import { AppWrapper } from "../../components/AppWrapper";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";

const app = document.getElementById("app-analytics");
const articles: Analytics.Article[] = JSON.parse(
  document.getElementById("data-articles")?.textContent || "[]"
);
if (app) {
  const latestArticle = articles[0];
  useAnalyticsStore.setState({
    selected: latestArticle ? [latestArticle.id] : [],
  });

  createRoot(app).render(
    <AppWrapper boundaryName="AnalyticsPage">
      <Analytics articles={articles} />
    </AppWrapper>
  );
}
