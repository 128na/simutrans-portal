import { createRoot } from "react-dom/client";
import { AppWrapper } from "../../components/AppWrapper";
import {
  RecentTrend,
  type RecentTrendData,
} from "../../features/analytics/RecentTrend";

const app = document.getElementById("app-mypage-trend");
if (app) {
  const trend: RecentTrendData = JSON.parse(
    document.getElementById("data-trend")?.textContent || "null"
  ) ?? { start_date: "", end_date: "", analytics: [] };

  createRoot(app).render(
    <AppWrapper boundaryName="DashboardPage">
      <RecentTrend trend={trend} />
    </AppWrapper>
  );
}
