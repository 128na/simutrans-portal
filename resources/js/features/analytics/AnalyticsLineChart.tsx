import {
  CartesianGrid,
  Legend,
  Line,
  LineChart,
  Symbols,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";
import type { DotItemDotProps } from "recharts";
import {
  axisStyles,
  formatPeriodLabel,
  markerShapeFor,
  spansMultipleYears,
  type ChartRow,
  type ChartSeries,
  type MarkerShape,
} from "./analyticsChart";

type Props = {
  type: Analytics.Type;
  rows: ChartRow[];
  series: ChartSeries[];
};

/** マーカーを全点に付けると密になるので、おおよそこの数まで間引く */
const MAX_MARKERS = 16;

const renderMarker =
  (shape: MarkerShape, axis: Analytics.Axis, step: number) =>
  (props: DotItemDotProps) => {
    const { cx, cy, index } = props;
    if (cx == null || cy == null || index % step !== 0) {
      return <g key={`dot-${index}`} />;
    }
    const { stroke } = axisStyles[axis];
    return (
      <Symbols
        key={`dot-${index}`}
        type={shape}
        cx={cx}
        cy={cy}
        size={48}
        stroke={stroke}
        strokeWidth={2}
        // PV は塗りつぶし、CV は白抜きにして線種以外でも区別する
        fill={axis === "pv" ? stroke : "#fff"}
      />
    );
  };

/**
 * PV・CV の推移を描く折れ線グラフ。rows は buildChartData で 0 埋めしたものを渡す
 */
export function AnalyticsLineChart({ type, rows, series }: Props) {
  const withYear =
    type === "daily" && spansMultipleYears(rows.map((row) => row.name));
  const step = Math.max(1, Math.ceil(rows.length / MAX_MARKERS));
  const articleIds = [...new Set(series.map((s) => s.id))];

  return (
    <LineChart
      style={{ width: "100%", minHeight: 300, maxHeight: 600 }}
      responsive
      data={rows}
    >
      <CartesianGrid strokeDasharray="3 3" stroke="#e3e6eb" />
      {series.map((s) => {
        const shape = markerShapeFor(articleIds.indexOf(s.id));
        const { stroke, strokeDasharray } = axisStyles[s.axis];
        return (
          <Line
            key={s.key}
            dataKey={s.key}
            name={s.name}
            stroke={stroke}
            strokeWidth={2}
            strokeDasharray={strokeDasharray}
            legendType={shape}
            dot={renderMarker(shape, s.axis, step)}
            isAnimationActive={false}
          />
        );
      })}
      <XAxis
        dataKey="name"
        tickFormatter={(period: string) =>
          formatPeriodLabel(type, period, withYear)
        }
        stroke="#5a6472"
      />
      <YAxis
        allowDecimals={false}
        stroke="#5a6472"
        label={{ value: "件数", angle: -90, position: "insideLeft" }}
      />
      <Legend />
      <Tooltip
        labelFormatter={(period) =>
          formatPeriodLabel(type, String(period), true)
        }
      />
    </LineChart>
  );
}
