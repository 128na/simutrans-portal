import { FormCaption } from "@/components/ui/FormCaption";
import TextSub from "@/components/ui/TextSub";
import Button from "@/components/ui/Button";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";
import axios from "axios";
import { useEffect, useRef, useState } from "react";
import { format } from "date-fns";
import {
  CartesianGrid,
  Legend,
  Line,
  LineChart,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";

type Props = {
  articles: Analytics.Article[];
};

type DataSet = ({ name: string } & Record<string, number | string>)[];
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

export function AnalyticsGraph({ articles }: Props) {
  const { selected, start_date, end_date, type, axes, mode } =
    useAnalyticsStore();

  const conditions = {
    ids: selected,
    type,
    start_date: format(start_date, "yyyy-MM-dd"),
    end_date: format(end_date, "yyyy-MM-dd"),
    mode,
    axes,
  };
  const key = JSON.stringify(conditions);
  const [request, setRequest] = useState<RequestState | null>(null);
  const latestRequest = useRef<AbortController | null>(null);
  const result = request?.phase === "ready" ? request : null;
  const analytics = result?.analytics ?? [];
  const appliedAxes = result?.conditions.axes ?? [];
  const validDates = conditions.start_date < conditions.end_date;

  useEffect(() => () => latestRequest.current?.abort(), []);

  // convert from analytics
  const keys: {
    key: string;
    name: string;
    stroke: string;
    id: number;
    axis: Analytics.Axis;
  }[] = [];
  const data: DataSet = [];
  analytics.forEach((an, idx) => {
    const { id, viewCounts, conversionCounts } = an;
    const article = articles.find((a) => a.id === id);
    if (!article) return;
    if (appliedAxes.includes("pv")) {
      const pvKey = `${article.id}-pv`;
      keys.push({
        key: pvKey,
        name: `${article.title} (PV)`,
        axis: "pv",
        stroke: `hsl(${211 + idx * 53}, 82%, 54%)`,
        id: article.id,
      });
      Object.entries(viewCounts).forEach(([date, value]) => {
        const d = data.find((d) => d.name === date);
        if (d) {
          d[pvKey] = value;
        } else {
          data.push({ name: date, [pvKey]: value });
        }
      });
    }
    if (appliedAxes.includes("cv")) {
      const cvKey = `${article.id}-cv`;
      keys.push({
        key: cvKey,
        name: `${article.title} (CV)`,
        axis: "cv",
        stroke: `hsl(${211 + idx * 53}, 63%, 76%)`,
        id: article.id,
      });
      Object.entries(conversionCounts).forEach(([date, value]) => {
        const d = data.find((d) => d.name === date);
        if (d) {
          d[cvKey] = value;
        } else {
          data.push({ name: date, [cvKey]: value });
        }
      });
    }
  });

  // 日付順にソート。過去データが十分にないと順序が狂うことがあるため
  data.sort((a, b) => (a.name < b.name ? -1 : 1));

  // 積算なら過去値も加えて加算する
  if (result?.conditions.mode === "cumulative") {
    const cumulativeData: DataSet = [];
    const cumulativeSums: Record<string, number> = {};
    data.forEach((entry, idx) => {
      const newEntry: { name: string } & Record<string, number | string> = {
        name: entry.name,
      };
      keys.forEach((key) => {
        // 日付順に並んでいるの最初だけ過去データの加算
        if (idx === 0) {
          const analytic = analytics.find((a) => a.id === key.id);
          if (!analytic) return;
          cumulativeSums[key.key] =
            key.axis === "pv"
              ? analytic.pastViewCount
              : analytic.pastConversionCount;
        }
        // 都度加算
        const value =
          typeof entry[key.key] === "number" ? (entry[key.key] as number) : 0;
        cumulativeSums[key.key] = (cumulativeSums[key.key] || 0) + value;

        newEntry[key.key] = cumulativeSums[key.key];
      });
      cumulativeData.push(newEntry);
    });
    data.splice(0, data.length, ...cumulativeData);
  }

  const onClick = async () => {
    latestRequest.current?.abort();
    const controller = new AbortController();
    latestRequest.current = controller;
    setRequest({ phase: "loading", key });
    const { ids, type, start_date, end_date } = conditions;

    try {
      const response = await axios.post<{ data: Analytics.ArticleAnalytic[] }>(
        "/api/v2/analytics",
        { ids, type, start_date, end_date },
        { signal: controller.signal }
      );
      if (!controller.signal.aborted) {
        setRequest({
          phase: "ready",
          key,
          conditions,
          analytics: response.data.data,
        });
      }
    } catch {
      if (!controller.signal.aborted) {
        setRequest({ phase: "error", key });
      }
    }
  };

  return (
    <div>
      <FormCaption>グラフ</FormCaption>
      <div className="space-y-2 mb-4">
        <Button
          onClick={onClick}
          disabled={
            selected.length === 0 ||
            axes.length === 0 ||
            !validDates ||
            (request?.phase === "loading" && request.key === key)
          }
          size="lg"
        >
          グラフを表示
        </Button>
        {selected.length === 0 && <TextSub>記事を選んでください</TextSub>}
        {!validDates && <TextSub>終了日は開始日より後にしてください。</TextSub>}
        {axes.length === 0 && (
          <TextSub>表示データを1つ以上選んでください。</TextSub>
        )}
        {(selected.length > 0 || request !== null) && request?.key !== key && (
          <p role="status" className="text-sm text-c-sub">
            条件は未適用です。「グラフを表示」で反映してください。
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
        (data.length === 0 ? (
          <TextSub>適用した条件に該当する集計はありません。</TextSub>
        ) : (
          <LineChart
            style={{ width: "100%", minHeight: 300, maxHeight: 600 }}
            responsive
            data={data}
          >
            <CartesianGrid strokeDasharray="3 3" />
            {keys.map((key) => (
              <Line
                key={key.key}
                dataKey={key.key}
                name={key.name}
                stroke={key.stroke}
                connectNulls
              />
            ))}
            <XAxis dataKey="name" />
            <YAxis
              label={{ value: "件数", angle: -90, position: "insideLeft" }}
            />
            <Legend />
            <Tooltip />
          </LineChart>
        ))}
    </div>
  );
}
