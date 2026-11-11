import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { render, screen } from "@testing-library/react";
import { RecentTrend } from "@/features/analytics/RecentTrend";

vi.mock("recharts", () => ({
  LineChart: ({
    children,
    data,
  }: {
    children: React.ReactNode;
    data: unknown;
  }) => (
    <div data-testid="line-chart" data-chart-data={JSON.stringify(data)}>
      {children}
    </div>
  ),
  Line: ({ dataKey, name }: { dataKey: string; name: string }) => (
    <div data-testid={`line-${name}`} data-key={dataKey} />
  ),
  Symbols: () => null,
  XAxis: () => null,
  YAxis: () => null,
  CartesianGrid: () => null,
  Legend: () => null,
  Tooltip: () => null,
}));

describe("RecentTrend", () => {
  beforeEach(() => {
    vi.useFakeTimers({ toFake: ["Date"] });
    vi.setSystemTime(new Date(2026, 10, 11, 12));
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it("対象の記事が無いときは案内を出し、グラフは描かない", () => {
    render(
      <RecentTrend
        trend={{
          start_date: "2026-10-13",
          end_date: "2026-11-11",
          analytics: [],
        }}
      />
    );

    expect(
      screen.getByText(/記事を公開すると、ここに表示回数とダウンロード数の推移/)
    ).toBeInTheDocument();
    expect(screen.queryByTestId("line-chart")).not.toBeInTheDocument();
  });

  it("記事ごとの PV・CV を日ごとに合計し、期間の全日を 0 埋めして PV・CV の2本で描く", () => {
    render(
      <RecentTrend
        trend={{
          start_date: "2026-10-13",
          end_date: "2026-11-11",
          analytics: [
            {
              id: 31,
              viewCounts: { "20261013": 4, "20261111": 9 },
              conversionCounts: { "20261111": 2 },
              pastViewCount: 50,
              pastConversionCount: 5,
            },
            {
              id: 32,
              viewCounts: { "20261111": 6 },
              conversionCounts: { "20261020": 1 },
              pastViewCount: 70,
              pastConversionCount: 8,
            },
          ],
        }}
      />
    );

    expect(screen.getByTestId("line-合計 (PV)")).toHaveAttribute(
      "data-key",
      "0-pv"
    );
    expect(screen.getByTestId("line-合計 (CV)")).toHaveAttribute(
      "data-key",
      "0-cv"
    );

    const rows = JSON.parse(
      screen.getByTestId("line-chart").getAttribute("data-chart-data") ?? "[]"
    ) as Record<string, number | string>[];
    expect(rows).toHaveLength(30);
    expect(rows[0]).toEqual({ name: "20261013", "0-pv": 4, "0-cv": 0 });
    expect(rows.find((row) => row.name === "20261020")).toEqual({
      name: "20261020",
      "0-pv": 0,
      "0-cv": 1,
    });
    // 累計ではなく日ごとの値（過去分は足さない）
    expect(rows[29]).toEqual({ name: "20261111", "0-pv": 15, "0-cv": 2 });
  });
});
