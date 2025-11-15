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
  const { selected, start_date, end_date, type, axes } = useAnalyticsStore();

  const [analytics, setAnalytics] = useState<Analytics.ArticleAnalytic[]>([]);

  // convert from analytics
  const keys: { name: string; stroke: string }[] = [];
  const data: DataSet = [];
  analytics.forEach((an, idx) => {
    const [articleId, pvs, cvs] = an;
    const article = articles.find((a) => a.id === articleId);
    if (!article) return;
    if (axes.includes("pv")) {
      const pvKey = `${article.title} (PV)`;
      keys.push({
        name: pvKey,
        stroke: `hsl(${211 + idx * 53}, 82%, 54%)`,
      });
      Object.entries(pvs).forEach(([date, value]) => {
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
      keys.push({ name: cvKey, stroke: `hsl(${211 + idx * 53}, 63%, 76%)` });
      Object.entries(cvs).forEach(([date, value]) => {
        const d = data.find((d) => d.name === date);
        if (d) {
          d[cvKey] = value;
        } else {
          data.push({ name: date, [cvKey]: value });
        }
      });
    }
  });

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
