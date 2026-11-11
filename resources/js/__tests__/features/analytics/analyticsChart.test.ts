import { describe, it, expect } from "vitest";
import {
  axisStyles,
  buildChartData,
  enumeratePeriods,
  formatPeriodLabel,
  markerShapeFor,
  spansMultipleYears,
  sumAnalytics,
  type ChartConditions,
} from "@/features/analytics/analyticsChart";

describe("enumeratePeriods", () => {
  it("日次は始端と終端を含めて並べる", () => {
    expect(enumeratePeriods("daily", "2026-11-04", "2026-11-10")).toEqual([
      "20261104",
      "20261105",
      "20261106",
      "20261107",
      "20261108",
      "20261109",
      "20261110",
    ]);
  });

  it("日次は月末を跨ぐ", () => {
    expect(enumeratePeriods("daily", "2026-01-30", "2026-02-02")).toEqual([
      "20260130",
      "20260131",
      "20260201",
      "20260202",
    ]);
  });

  it("うるう年は2/29を含み、平年は含まない", () => {
    expect(enumeratePeriods("daily", "2024-02-28", "2024-03-01")).toEqual([
      "20240228",
      "20240229",
      "20240301",
    ]);
    expect(enumeratePeriods("daily", "2025-02-28", "2025-03-01")).toEqual([
      "20250228",
      "20250301",
    ]);
  });

  it("日次は年を跨ぐ", () => {
    expect(enumeratePeriods("daily", "2025-12-31", "2026-01-01")).toEqual([
      "20251231",
      "20260101",
    ]);
  });

  it("月次は月の途中の日付でも両端の月を含め、年を跨ぐ", () => {
    expect(enumeratePeriods("monthly", "2025-11-30", "2026-02-01")).toEqual([
      "202511",
      "202512",
      "202601",
      "202602",
    ]);
  });

  it("年次は両端の年を含む", () => {
    expect(enumeratePeriods("yearly", "2023-12-31", "2026-01-01")).toEqual([
      "2023",
      "2024",
      "2025",
      "2026",
    ]);
  });

  it("開始日が終了日より後なら空", () => {
    expect(enumeratePeriods("daily", "2026-11-10", "2026-11-09")).toEqual([]);
  });
});

describe("formatPeriodLabel", () => {
  it("単位に応じた短い表記にする", () => {
    expect(formatPeriodLabel("daily", "20261110")).toBe("11/10");
    expect(formatPeriodLabel("daily", "20260105")).toBe("1/5");
    expect(formatPeriodLabel("monthly", "202611")).toBe("2026/11");
    expect(formatPeriodLabel("yearly", "2026")).toBe("2026");
  });

  it("日次は指定すると年を付ける", () => {
    expect(formatPeriodLabel("daily", "20251231", true)).toBe("2025/12/31");
  });

  it("解釈できない値はそのまま返す", () => {
    expect(formatPeriodLabel("daily", "unknown")).toBe("unknown");
  });
});

describe("spansMultipleYears", () => {
  it("先頭と末尾の年が違うときだけ true", () => {
    expect(spansMultipleYears(["20251231", "20260101"])).toBe(true);
    expect(spansMultipleYears(["20260101", "20261231"])).toBe(false);
    expect(spansMultipleYears([])).toBe(false);
  });
});

describe("buildChartData", () => {
  /** 期間より後の固定日。実行日に左右されないようにする */
  const TODAY = new Date(2030, 0, 1);
  const articles = [
    { id: 11, title: "記事A" },
    { id: 22, title: "記事B" },
  ];
  const base: ChartConditions = {
    type: "daily",
    start_date: "2026-11-04",
    end_date: "2026-11-10",
    mode: "periodic",
    axes: ["pv", "cv"],
  };

  it("データのない日を 0 で埋め、期間の全区間を並べる", () => {
    const { series, rows } = buildChartData(
      [
        {
          id: 11,
          viewCounts: { "20261105": 3, "20261110": 7 },
          conversionCounts: { "20261110": 2 },
          pastViewCount: 100,
          pastConversionCount: 50,
        },
      ],
      articles,
      base,
      TODAY
    );

    expect(series.map((s) => s.key)).toEqual(["11-pv", "11-cv"]);
    expect(series.map((s) => s.name)).toEqual(["記事A (PV)", "記事A (CV)"]);
    expect(rows.map((r) => r.name)).toEqual([
      "20261104",
      "20261105",
      "20261106",
      "20261107",
      "20261108",
      "20261109",
      "20261110",
    ]);
    expect(rows.map((r) => r["11-pv"])).toEqual([0, 3, 0, 0, 0, 0, 7]);
    expect(rows.map((r) => r["11-cv"])).toEqual([0, 0, 0, 0, 0, 0, 2]);
  });

  it("期間内のデータが0件でも全区間を 0 で並べる", () => {
    const { series, rows } = buildChartData(
      [
        {
          id: 22,
          viewCounts: {},
          conversionCounts: {},
          pastViewCount: 0,
          pastConversionCount: 0,
        },
      ],
      articles,
      { ...base, axes: ["pv"] },
      TODAY
    );

    expect(series.map((s) => s.key)).toEqual(["22-pv"]);
    expect(rows).toHaveLength(7);
    expect(rows.every((r) => r["22-pv"] === 0)).toBe(true);
  });

  it("累積は期間前の合計から始め、0 の区間も前の値を引き継ぐ", () => {
    const { rows } = buildChartData(
      [
        {
          id: 11,
          viewCounts: { "20261105": 3, "20261110": 7 },
          conversionCounts: { "20261106": 1 },
          pastViewCount: 100,
          pastConversionCount: 50,
        },
      ],
      articles,
      { ...base, mode: "cumulative" },
      TODAY
    );

    expect(rows.map((r) => r["11-pv"])).toEqual([
      100, 103, 103, 103, 103, 103, 110,
    ]);
    expect(rows.map((r) => r["11-cv"])).toEqual([50, 50, 51, 51, 51, 51, 51]);
  });

  it("月次・年次も期間の全区間を並べる", () => {
    const analytic = {
      id: 22,
      viewCounts: { "202601": 4 },
      conversionCounts: {},
      pastViewCount: 0,
      pastConversionCount: 0,
    };
    const monthly = buildChartData(
      [analytic],
      articles,
      {
        ...base,
        type: "monthly",
        start_date: "2025-11-15",
        end_date: "2026-02-10",
        axes: ["pv"],
      },
      TODAY
    );
    expect(monthly.rows.map((r) => [r.name, r["22-pv"]])).toEqual([
      ["202511", 0],
      ["202512", 0],
      ["202601", 4],
      ["202602", 0],
    ]);

    const yearly = buildChartData(
      [{ ...analytic, viewCounts: { "2025": 9 } }],
      articles,
      {
        ...base,
        type: "yearly",
        start_date: "2024-06-01",
        end_date: "2026-01-01",
        axes: ["pv"],
      },
      TODAY
    );
    expect(yearly.rows.map((r) => [r.name, r["22-pv"]])).toEqual([
      ["2024", 0],
      ["2025", 9],
      ["2026", 0],
    ]);
  });

  it("終了日が今日より後なら今日までの区間にする", () => {
    const { rows } = buildChartData(
      [
        {
          id: 11,
          viewCounts: { "20261109": 1 },
          conversionCounts: {},
          pastViewCount: 0,
          pastConversionCount: 0,
        },
      ],
      articles,
      { ...base, end_date: "2026-11-20", axes: ["pv"] },
      new Date(2026, 10, 9, 15, 0)
    );
    expect(rows.map((r) => [r.name, r["11-pv"]])).toEqual([
      ["20261104", 0],
      ["20261105", 0],
      ["20261106", 0],
      ["20261107", 0],
      ["20261108", 0],
      ["20261109", 1],
    ]);
  });

  it("記事一覧にない ID は系列にしない。系列がなければ行も空", () => {
    expect(
      buildChartData(
        [
          {
            id: 999,
            viewCounts: { "20261105": 1 },
            conversionCounts: {},
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
        articles,
        base,
        TODAY
      )
    ).toEqual({ series: [], rows: [] });
  });
});

describe("系列の見た目", () => {
  it("PV と CV は色と線種が違う", () => {
    expect(axisStyles.pv.stroke).not.toBe(axisStyles.cv.stroke);
    expect(axisStyles.pv.strokeDasharray).toBeUndefined();
    expect(axisStyles.cv.strokeDasharray).toBeDefined();
  });

  it("マーカーの形は記事の順に4種類で一巡する", () => {
    expect([0, 1, 2, 3, 4].map(markerShapeFor)).toEqual([
      "circle",
      "square",
      "triangle",
      "diamond",
      "circle",
    ]);
  });
});

describe("sumAnalytics", () => {
  it("記事ごとの集計を区間ごとに足し合わせる", () => {
    const total = sumAnalytics(
      [
        {
          id: 11,
          viewCounts: { "20261109": 3, "20261110": 5 },
          conversionCounts: { "20261110": 1 },
          pastViewCount: 100,
          pastConversionCount: 7,
        },
        {
          id: 12,
          viewCounts: { "20261110": 2, "20261111": 4 },
          conversionCounts: { "20261110": 2, "20261111": 6 },
          pastViewCount: 20,
          pastConversionCount: 3,
        },
      ],
      99
    );

    expect(total).toEqual({
      id: 99,
      viewCounts: { "20261109": 3, "20261110": 7, "20261111": 4 },
      conversionCounts: { "20261110": 3, "20261111": 6 },
      pastViewCount: 120,
      pastConversionCount: 10,
    });
  });

  it("集計が空の記事（API では配列になる）があっても足し合わせられる", () => {
    const total = sumAnalytics(
      [
        {
          id: 21,
          viewCounts: [] as unknown as Record<string, number>,
          conversionCounts: [] as unknown as Record<string, number>,
          pastViewCount: 0,
          pastConversionCount: 0,
        },
        {
          id: 22,
          viewCounts: { "20261111": 8 },
          conversionCounts: {},
          pastViewCount: 0,
          pastConversionCount: 0,
        },
      ],
      0
    );

    expect(total.viewCounts).toEqual({ "20261111": 8 });
    expect(total.conversionCounts).toEqual({});
  });
});
