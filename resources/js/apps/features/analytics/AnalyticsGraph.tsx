import Button from "@/apps/components/ui/Button";
import TextSub from "@/apps/components/ui/TextSub";
import { useAnalyticsStore } from "@/apps/state/useAnalyticsStore";
import axios from "axios";
import { useState } from "react";
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
export function AnalyticsGraph({ articles }: Props) {
  const { selected, start_date, end_date, type, axes, mode } =
    useAnalyticsStore();

  const [analytics, setAnalytics] = useState<Analytics.ArticleAnalytic[]>([]);

  // convert from analytics
  const keys: { name: string; stroke: string; id: number }[] = [];
  const data: DataSet = [];
  analytics.forEach((an, idx) => {
    const { id, viewCounts, conversionCounts } = an;
    const article = articles.find((a) => a.id === id);
    if (!article) return;
    if (axes.includes("pv")) {
      const pvKey = `${article.title} (PV)`;
      keys.push({
        name: pvKey,
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
    if (axes.includes("cv")) {
      const cvKey = `${article.title} (CV)`;
      keys.push({
        name: cvKey,
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

  console.log(data);

  // 積算なら過去値も加えて加算する
  if (mode === "cumulative") {
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
          if (key.name.endsWith("(PV)")) {
            cumulativeSums[key.name] = analytic.pastViewCount;
          } else if (key.name.endsWith("(CV)")) {
            cumulativeSums[key.name] = analytic.pastConversionCount;
          }
        }
        // 都度加算
        const value =
          typeof entry[key.name] === "number" ? (entry[key.name] as number) : 0;
        cumulativeSums[key.name] = (cumulativeSums[key.name] || 0) + value;

        newEntry[key.name] = cumulativeSums[key.name];
      });
      cumulativeData.push(newEntry);
    });
    data.splice(0, data.length, ...cumulativeData);
  }

  const onClick = async () => {
    const res = await axios.post("/api/v2/analytics", {
      ids: selected,
      type,
      start_date,
      end_date,
    });

    setAnalytics(res.data.data);
  };

  return (
    <div>
      {data.length === 0 ? (
        <TextSub>グラフを表示するには記事を選んでください</TextSub>
      ) : (
        <LineChart
          style={{ width: "100%", aspectRatio: 2 }}
          responsive
          data={data}
        >
          <CartesianGrid strokeDasharray="3 3" />
          {keys.map((key) => (
            <Line
              key={key.name}
              dataKey={key.name}
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
      )}
      <Button onClick={onClick} disabled={selected.length < 1}>
        グラフを表示
      </Button>
    </div>
  );
}
