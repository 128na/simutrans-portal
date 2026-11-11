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

  it("公開中の記事が無いときは案内を出し、グラフは描かない", () => {
    render(
      <RecentTrend
        trend={{
          start_date: "2026-10-13",
          end_date: "2026-11-11",
          total: null,
        }}
      />
    );

    expect(
      screen.getByText(/記事を公開すると、ここに表示回数とダウンロード数の推移/)
    ).toBeInTheDocument();
    expect(screen.queryByTestId("line-chart")).not.toBeInTheDocument();
  });

  it("合計済みの PV・CV を、期間の全日を 0 埋めして PV・CV の2本で描く", () => {
    render(
      <RecentTrend
        trend={{
          start_date: "2026-10-13",
          end_date: "2026-11-11",
          total: {
            id: 0,
            viewCounts: { "20261013": 4, "20261111": 15 },
            conversionCounts: { "20261020": 1, "20261111": 2 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
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

  it("公開中の記事があるが期間内のデータが無いときも、0 の線を描く", () => {
    render(
      <RecentTrend
        trend={{
          start_date: "2026-10-13",
          end_date: "2026-11-11",
          total: {
            id: 0,
            viewCounts: [] as unknown as Record<string, number>,
            conversionCounts: [] as unknown as Record<string, number>,
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        }}
      />
    );

    const rows = JSON.parse(
      screen.getByTestId("line-chart").getAttribute("data-chart-data") ?? "[]"
    ) as Record<string, number | string>[];
    expect(rows).toHaveLength(30);
    expect(rows.every((row) => row["0-pv"] === 0 && row["0-cv"] === 0)).toBe(
      true
    );
  });
});
