import {
  eachDayOfInterval,
  eachMonthOfInterval,
  eachYearOfInterval,
  format,
  parse,
  parseISO,
} from "date-fns";

/**
 * グラフの1行。name は API の period（日次 yyyyMMdd・月次 yyyyMM・年次 yyyy）
 */
export type ChartRow = { name: string } & Record<string, number | string>;

export type ChartSeries = {
  key: string;
  name: string;
  axis: Analytics.Axis;
  id: number;
};

export type ChartConditions = {
  type: Analytics.Type;
  /** yyyy-MM-dd */
  start_date: string;
  /** yyyy-MM-dd */
  end_date: string;
  mode: Analytics.Mode;
  axes: Analytics.Axis[];
};

const periodFormats: Record<Analytics.Type, string> = {
  daily: "yyyyMMdd",
  monthly: "yyyyMM",
  yearly: "yyyy",
};

/**
 * 開始日〜終了日（両端を含む）の区間を API の period 形式で並べる
 */
export const enumeratePeriods = (
  type: Analytics.Type,
  startDate: string,
  endDate: string
): string[] => {
  const interval = { start: parseISO(startDate), end: parseISO(endDate) };
  if (interval.start > interval.end) return [];
  const dates =
    type === "daily"
      ? eachDayOfInterval(interval)
      : type === "monthly"
        ? eachMonthOfInterval(interval)
        : eachYearOfInterval(interval);
  return dates.map((date) => format(date, periodFormats[type]));
};

const parsePeriod = (type: Analytics.Type, period: string): Date =>
  parse(period, periodFormats[type], new Date(2000, 0, 1));

/**
 * 目盛り用の短い表記（日次 11/10、月次 2026/11、年次 2026）。
 * withYear を付けると日次にも年を付ける（年を跨ぐ期間向け）
 */
export const formatPeriodLabel = (
  type: Analytics.Type,
  period: string,
  withYear = false
): string => {
  const date = parsePeriod(type, period);
  if (Number.isNaN(date.getTime())) return period;
  if (type === "daily") return format(date, withYear ? "yyyy/M/d" : "M/d");
  if (type === "monthly") return format(date, "yyyy/M");
  return format(date, "yyyy");
};

/**
 * 区間の並びが年を跨ぐか（日次の目盛りに年を付けるかの判定）
 */
export const spansMultipleYears = (periods: string[]): boolean =>
  periods.length > 0 &&
  periods[0].slice(0, 4) !== periods[periods.length - 1].slice(0, 4);

/**
 * API の集計を、指定期間の全区間を 0 で埋めたグラフ用の系列と行に変換する
 */
export const buildChartData = (
  analytics: Analytics.ArticleAnalytic[],
  articles: Pick<Analytics.Article, "id" | "title">[],
  conditions: ChartConditions
): { series: ChartSeries[]; rows: ChartRow[] } => {
  const periods = enumeratePeriods(
    conditions.type,
    conditions.start_date,
    conditions.end_date
  );
  const series: ChartSeries[] = [];
  const counts: Record<string, Record<string, number>> = {};
  const pastCounts: Record<string, number> = {};

  analytics.forEach((analytic) => {
    const article = articles.find((a) => a.id === analytic.id);
    if (!article) return;
    (["pv", "cv"] as const).forEach((axis) => {
      if (!conditions.axes.includes(axis)) return;
      const key = `${article.id}-${axis}`;
      series.push({
        key,
        name: `${article.title} (${axis.toUpperCase()})`,
        axis,
        id: article.id,
      });
      counts[key] =
        axis === "pv" ? analytic.viewCounts : analytic.conversionCounts;
      pastCounts[key] =
        axis === "pv" ? analytic.pastViewCount : analytic.pastConversionCount;
    });
  });

  if (series.length === 0) return { series, rows: [] };

  const cumulative = conditions.mode === "cumulative";
  const sums: Record<string, number> = {};
  series.forEach((s) => {
    sums[s.key] = cumulative ? pastCounts[s.key] : 0;
  });

  const rows = periods.map((period) => {
    const row: ChartRow = { name: period };
    series.forEach((s) => {
      const value = Number(counts[s.key]?.[period] ?? 0);
      if (cumulative) {
        sums[s.key] += value;
        row[s.key] = sums[s.key];
      } else {
        row[s.key] = value;
      }
    });
    return row;
  });

  return { series, rows };
};

/**
 * 系列の見た目。PV と CV は色相・明るさ・線種で区別し、記事はマーカーの形で区別する
 */
export const axisStyles = {
  /** 青（c-primary）・実線。明るさ OKLab L 0.53 */
  pv: { stroke: "#1565d8", strokeDasharray: undefined },
  /** 橙・破線。PV より明るい（OKLab L 0.65） */
  cv: { stroke: "#ea580c", strokeDasharray: "6 3" },
} as const satisfies Record<
  Analytics.Axis,
  { stroke: string; strokeDasharray: string | undefined }
>;

export const markerShapes = [
  "circle",
  "square",
  "triangle",
  "diamond",
] as const;
export type MarkerShape = (typeof markerShapes)[number];

/**
 * 記事の並び順（index）からマーカーの形を決める。4種類で一巡する
 */
export const markerShapeFor = (index: number): MarkerShape =>
  markerShapes[index % markerShapes.length];
