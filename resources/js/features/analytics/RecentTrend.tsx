import { AnalyticsLineChart } from "./AnalyticsLineChart";
import { buildChartData } from "./analyticsChart";

export type RecentTrendData = {
  /** yyyy-MM-dd */
  start_date: string;
  /** yyyy-MM-dd */
  end_date: string;
  /** ユーザー全体の日次合計（合計済みの1件）。公開中の記事が無いときは null */
  total: Analytics.ArticleAnalytic | null;
};

type Props = {
  trend: RecentTrendData;
};

/** 合計の系列に使う仮の記事 */
const TOTAL = { id: 0, title: "合計" };

/**
 * マイページトップの直近の推移。サーバーで合計済みの日次 PV・CV を1本ずつ描く
 */
export const RecentTrend = ({ trend }: Props) => {
  if (trend.total === null) {
    return (
      <p className="text-c-sub">
        記事を公開すると、ここに表示回数とダウンロード数の推移が出ます。
      </p>
    );
  }

  const { rows, series } = buildChartData([trend.total], [TOTAL], {
    type: "daily",
    start_date: trend.start_date,
    end_date: trend.end_date,
    mode: "periodic",
    axes: ["pv", "cv"],
  });

  return <AnalyticsLineChart type="daily" rows={rows} series={series} />;
};
