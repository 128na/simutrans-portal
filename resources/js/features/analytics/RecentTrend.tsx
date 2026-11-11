import { AnalyticsLineChart } from "./AnalyticsLineChart";
import { buildChartData, sumAnalytics } from "./analyticsChart";

export type RecentTrendData = {
  /** yyyy-MM-dd */
  start_date: string;
  /** yyyy-MM-dd */
  end_date: string;
  analytics: Analytics.ArticleAnalytic[];
};

type Props = {
  trend: RecentTrendData;
};

/** 合計の系列に使う仮の記事 */
const TOTAL = { id: 0, title: "合計" };

/**
 * マイページトップの直近の推移。対象記事の日次 PV・CV を合計して1本ずつ描く
 */
export const RecentTrend = ({ trend }: Props) => {
  if (trend.analytics.length === 0) {
    return (
      <p className="text-c-sub">
        記事を公開すると、ここに表示回数とダウンロード数の推移が出ます。
      </p>
    );
  }

  const { rows, series } = buildChartData(
    [sumAnalytics(trend.analytics, TOTAL.id)],
    [TOTAL],
    {
      type: "daily",
      start_date: trend.start_date,
      end_date: trend.end_date,
      mode: "periodic",
      axes: ["pv", "cv"],
    }
  );

  return <AnalyticsLineChart type="daily" rows={rows} series={series} />;
};
