import { FormCaption } from "@/components/ui/FormCaption";
import TextSub from "@/components/ui/TextSub";
import Button from "@/components/ui/Button";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";
import axios from "axios";
import { useCallback, useEffect, useRef, useState } from "react";
import { format } from "date-fns";
import { buildChartData } from "./analyticsChart";
import { AnalyticsLineChart } from "./AnalyticsLineChart";

type Props = {
  articles: Analytics.Article[];
  loadOnMount?: boolean;
};

type Conditions = {
  ids: number[];
  type: Analytics.Type;
  start_date: string;
  end_date: string;
  mode: Analytics.Mode;
  axes: Analytics.Axis[];
};

type RequestState =
  | { phase: "loading" | "error"; key: string }
  | {
      phase: "ready";
      key: string;
      conditions: Conditions;
      analytics: Analytics.ArticleAnalytic[];
    };

const periodLength: Record<Analytics.Type, number> = {
  daily: 10,
  monthly: 7,
  yearly: 4,
};

const intervalNames: Record<Analytics.Type, string> = {
  daily: "日次",
  monthly: "月次",
  yearly: "年次",
};

export function AnalyticsGraph({ articles, loadOnMount = false }: Props) {
  const { selected, start_date, end_date, type, axes, mode } =
    useAnalyticsStore();

  const conditions: Conditions = {
    ids: selected,
    type,
    start_date: format(start_date, "yyyy-MM-dd"),
    end_date: format(end_date, "yyyy-MM-dd"),
    mode,
    axes,
  };
  const key = JSON.stringify(conditions);
  const validDates = conditions.start_date < conditions.end_date;
  const canFetch = selected.length > 0 && axes.length > 0 && validDates;
  const [initialConditions] = useState(() =>
    loadOnMount && canFetch ? conditions : null
  );
  const [request, setRequest] = useState<RequestState | null>(() =>
    initialConditions
      ? { phase: "loading", key: JSON.stringify(initialConditions) }
      : null
  );
  const latestRequest = useRef<AbortController | null>(null);
  const result = request?.phase === "ready" ? request : null;

  const fetchAnalytics = useCallback((requested: Conditions) => {
    latestRequest.current?.abort();
    const controller = new AbortController();
    latestRequest.current = controller;
    const requestKey = JSON.stringify(requested);
    const { ids, type, start_date, end_date } = requested;

    void axios
      .post<{ data: Analytics.ArticleAnalytic[] }>(
        "/api/v2/analytics",
        { ids, type, start_date, end_date },
        { signal: controller.signal }
      )
      .then(
        (response) => {
          if (!controller.signal.aborted) {
            setRequest({
              phase: "ready",
              key: requestKey,
              conditions: requested,
              analytics: response.data.data,
            });
          }
        },
        () => {
          if (!controller.signal.aborted) {
            setRequest({ phase: "error", key: requestKey });
          }
        }
      );
  }, []);

  useEffect(() => {
    if (initialConditions) fetchAnalytics(initialConditions);
    return () => latestRequest.current?.abort();
  }, [fetchAnalytics, initialConditions]);

  const { series, rows } = result
    ? buildChartData(result.analytics, articles, result.conditions)
    : { series: [], rows: [] };

  const onClick = () => {
    setRequest({ phase: "loading", key });
    fetchAnalytics(conditions);
  };

  return (
    <div>
      <FormCaption>グラフ</FormCaption>
      <div className="space-y-2 mb-4">
        {selected.length === 0 && <TextSub>記事を選んでください</TextSub>}
        {!validDates && <TextSub>終了日は開始日より後にしてください。</TextSub>}
        {axes.length === 0 && (
          <TextSub>表示データを1つ以上選んでください。</TextSub>
        )}
        {(selected.length > 0 || request !== null) && request?.key !== key && (
          <p role="status" className="text-sm text-c-sub">
            条件は未適用です。「グラフを再表示」で反映してください。
          </p>
        )}
        {request?.phase === "loading" && (
          <p role="status">集計を取得しています。</p>
        )}
        {request?.phase === "error" && (
          <p role="alert">集計の取得に失敗しました。もう一度お試しください。</p>
        )}
        {result && (
          <div className="text-sm text-c-sub">
            <p>
              適用済み：
              {result.conditions.start_date.slice(
                0,
                periodLength[result.conditions.type]
              )}
              〜
              {result.conditions.end_date.slice(
                0,
                periodLength[result.conditions.type]
              )}
              （{intervalNames[result.conditions.type]}・
              {result.conditions.mode === "cumulative" ? "累積" : "期間別"}）
            </p>
            <p>
              対象記事：
              {result.conditions.ids
                .map(
                  (id) =>
                    articles.find((article) => article.id === id)?.title ??
                    String(id)
                )
                .join("、")}
            </p>
          </div>
        )}
      </div>
      {result &&
        (series.length === 0 ? (
          <TextSub>適用した条件に該当する集計はありません。</TextSub>
        ) : (
          <AnalyticsLineChart
            type={result.conditions.type}
            rows={rows}
            series={series}
          />
        ))}
      <div className="mt-4">
        <Button
          onClick={onClick}
          disabled={
            !canFetch || (request?.phase === "loading" && request.key === key)
          }
          size="lg"
        >
          グラフを再表示
        </Button>
      </div>
    </div>
  );
}
