import { describe, it, expect, vi, beforeEach } from "vitest";
import { act, render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { AnalyticsGraph } from "@/features/analytics/AnalyticsGraph";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";
import axios from "axios";

// モック
vi.mock("@/hooks/useAnalyticsStore");
vi.mock("axios");

// Recharts のモック
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
  Line: ({
    dataKey,
    name,
    stroke,
    strokeDasharray,
    legendType,
  }: {
    dataKey: string;
    name: string;
    stroke: string;
    strokeDasharray?: string;
    legendType?: string;
  }) => (
    <div
      data-testid={`line-${name}`}
      data-key={dataKey}
      data-name={name}
      data-stroke={stroke}
      data-dash={strokeDasharray ?? ""}
      data-legend-type={legendType}
    />
  ),
  Symbols: () => <div data-testid="symbols" />,
  XAxis: ({ tickFormatter }: { tickFormatter: (value: string) => string }) => (
    <div data-testid="x-axis" data-first-tick={tickFormatter("20240101")} />
  ),
  YAxis: () => <div data-testid="y-axis" />,
  CartesianGrid: () => <div data-testid="cartesian-grid" />,
  Legend: () => <div data-testid="legend" />,
  Tooltip: () => <div data-testid="tooltip" />,
}));

describe("AnalyticsGraph", () => {
  const mockArticles: Analytics.Article[] = [
    { id: 1, title: "Article 1" } as Analytics.Article,
    { id: 2, title: "Article 2" } as Analytics.Article,
  ];

  // @ts-expect-error - Mock for testing
  const mockUseAnalyticsStore = useAnalyticsStore;
  const mockAxios = axios;

  beforeEach(() => {
    vi.clearAllMocks();
    mockUseAnalyticsStore.mockReturnValue({
      selected: [1, 2],
      start_date: "2024-01-01",
      end_date: "2024-01-31",
      type: "daily",
      axes: ["pv", "cv"],
      mode: "normal",
    });
  });

  it("初期状態では記事を選んでくださいと表示される", () => {
    mockUseAnalyticsStore.mockReturnValue({
      selected: [],
      start_date: "2024-01-01",
      end_date: "2024-01-31",
      type: "daily",
      axes: ["pv"],
      mode: "normal",
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    expect(screen.getByText("記事を選んでください")).toBeInTheDocument();
  });

  it("初期選択で一度だけ取得し、条件を変更しただけでは再取得しない", async () => {
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10 },
            conversionCounts: {},
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    const { rerender } = render(
      <AnalyticsGraph articles={mockArticles} loadOnMount />
    );
    expect(await screen.findByTestId("line-chart")).toBeInTheDocument();
    expect(mockAxios.post).toHaveBeenCalledExactlyOnceWith(
      "/api/v2/analytics",
      {
        ids: [1, 2],
        type: "daily",
        start_date: "2024-01-01",
        end_date: "2024-01-31",
      },
      { signal: expect.any(AbortSignal) }
    );

    mockUseAnalyticsStore.mockReturnValue({
      selected: [2],
      start_date: "2024-01-01",
      end_date: "2024-01-15",
      type: "daily",
      axes: ["pv"],
      mode: "periodic",
    });
    rerender(<AnalyticsGraph articles={mockArticles} loadOnMount />);
    expect(screen.getByText(/条件は未適用/)).toBeInTheDocument();
    expect(mockAxios.post).toHaveBeenCalledTimes(1);
  });

  it("グラフを再表示ボタンが表示される", () => {
    render(<AnalyticsGraph articles={mockArticles} />);
    expect(
      screen.getByRole("button", { name: "グラフを再表示" })
    ).toBeInTheDocument();
  });

  it("記事が選択されていない場合ボタンが無効になる", () => {
    mockUseAnalyticsStore.mockReturnValue({
      selected: [],
      start_date: "2024-01-01",
      end_date: "2024-01-31",
      type: "daily",
      axes: ["pv"],
      mode: "normal",
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });
    expect(button).toBeDisabled();
  });

  it("ボタンをクリックするとAPI呼び出しが行われる", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10, "20240102": 20 },
            conversionCounts: { "20240101": 5, "20240102": 8 },
            pastViewCount: 100,
            pastConversionCount: 50,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      expect(mockAxios.post).toHaveBeenCalledWith(
        "/api/v2/analytics",
        {
          ids: [1, 2],
          type: "daily",
          start_date: "2024-01-01",
          end_date: "2024-01-31",
        },
        { signal: expect.any(AbortSignal) }
      );
    });
  });

  it("APIレスポンスを受けてグラフが表示される", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10 },
            conversionCounts: { "20240101": 5 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      expect(screen.getByTestId("line-chart")).toBeInTheDocument();
    });
  });

  it("PVとCVの両方の軸を表示する", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10 },
            conversionCounts: { "20240101": 5 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      expect(screen.getByTestId("line-Article 1 (PV)")).toBeInTheDocument();
      expect(screen.getByTestId("line-Article 1 (CV)")).toBeInTheDocument();
    });
  });

  it("期間の全日を0埋めし、PVとCVを色と線種で区別して読みやすい日付で表示する", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240105": 10 },
            conversionCounts: { "20240110": 5 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
          {
            id: 2,
            viewCounts: {},
            conversionCounts: {},
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    await user.click(screen.getByRole("button", { name: "グラフを再表示" }));

    const chart = await screen.findByTestId("line-chart");
    const chartData = JSON.parse(chart.getAttribute("data-chart-data") || "[]");
    expect(chartData).toHaveLength(31);
    expect(chartData[0]).toEqual({
      name: "20240101",
      "1-pv": 0,
      "1-cv": 0,
      "2-pv": 0,
      "2-cv": 0,
    });
    expect(chartData[4]["1-pv"]).toBe(10);
    expect(chartData[9]["1-cv"]).toBe(5);
    expect(chartData[30].name).toBe("20240131");

    const pv = screen.getByTestId("line-Article 1 (PV)");
    const cv = screen.getByTestId("line-Article 1 (CV)");
    expect(pv.dataset.stroke).not.toBe(cv.dataset.stroke);
    expect(pv.dataset.dash).toBe("");
    expect(cv.dataset.dash).not.toBe("");
    // 記事はマーカーの形で区別する
    expect(pv.dataset.legendType).toBe("circle");
    expect(screen.getByTestId("line-Article 2 (PV)").dataset.legendType).toBe(
      "square"
    );
    expect(screen.getByTestId("x-axis").dataset.firstTick).toBe("1/1");
  });

  it("PVのみの軸を表示する", async () => {
    const user = userEvent.setup();
    mockUseAnalyticsStore.mockReturnValue({
      selected: [1],
      start_date: "2024-01-01",
      end_date: "2024-01-31",
      type: "daily",
      axes: ["pv"],
      mode: "normal",
    });

    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10 },
            conversionCounts: { "20240101": 5 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      expect(screen.getByTestId("line-Article 1 (PV)")).toBeInTheDocument();
      expect(
        screen.queryByTestId("line-Article 1 (CV)")
      ).not.toBeInTheDocument();
    });
  });

  it("累積モードでグラフを表示する", async () => {
    const user = userEvent.setup();
    mockUseAnalyticsStore.mockReturnValue({
      selected: [1],
      start_date: "2024-01-01",
      end_date: "2024-01-31",
      type: "daily",
      axes: ["pv"],
      mode: "cumulative",
    });

    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10, "20240102": 20 },
            conversionCounts: {},
            pastViewCount: 100,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      const chart = screen.getByTestId("line-chart");
      const chartData = JSON.parse(
        chart.getAttribute("data-chart-data") || "[]"
      );
      // 累積なので、過去値100 + 10 = 110、110 + 20 = 130。以降は0埋めで130のまま
      expect(chartData).toHaveLength(31);
      expect(chartData[0]["1-pv"]).toBe(110);
      expect(chartData[1]["1-pv"]).toBe(130);
      expect(chartData[30]["1-pv"]).toBe(130);
    });
  });

  it("複数記事のデータを統合表示する", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: { "20240101": 10 },
            conversionCounts: { "20240101": 5 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
          {
            id: 2,
            viewCounts: { "20240101": 15 },
            conversionCounts: { "20240101": 8 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      expect(screen.getByTestId("line-Article 1 (PV)")).toBeInTheDocument();
      expect(screen.getByTestId("line-Article 2 (PV)")).toBeInTheDocument();
    });
  });

  it("日付順にデータがソートされる", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 1,
            viewCounts: {
              "20240103": 30,
              "20240101": 10,
              "20240102": 20,
            },
            conversionCounts: {},
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      const chart = screen.getByTestId("line-chart");
      const chartData = JSON.parse(
        chart.getAttribute("data-chart-data") || "[]"
      );
      expect(chartData[0].name).toBe("20240101");
      expect(chartData[1].name).toBe("20240102");
      expect(chartData[2].name).toBe("20240103");
    });
  });

  it("変更した条件の結果を、後から届いた旧レスポンスで上書きしない", async () => {
    const user = userEvent.setup();
    let resolveOld!: (value: {
      data: { data: Analytics.ArticleAnalytic[] };
    }) => void;
    const oldRequest = new Promise((resolve) => {
      resolveOld = resolve;
    });
    mockAxios.post = vi
      .fn()
      .mockReturnValueOnce(oldRequest)
      .mockResolvedValueOnce({ data: { data: [] } });

    const { rerender } = render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });
    await user.click(button);
    expect(screen.getByText("集計を取得しています。")).toBeInTheDocument();

    mockUseAnalyticsStore.mockReturnValue({
      selected: [2],
      start_date: "2024-02-01",
      end_date: "2024-02-29",
      type: "daily",
      axes: ["pv"],
      mode: "periodic",
    });
    rerender(<AnalyticsGraph articles={mockArticles} />);
    expect(screen.getByText(/条件は未適用/)).toBeInTheDocument();
    await user.click(button);
    expect(await screen.findByText("対象記事：Article 2")).toBeInTheDocument();

    await act(async () => {
      resolveOld({
        data: {
          data: [
            {
              id: 1,
              viewCounts: { "20240101": 10 },
              conversionCounts: {},
              pastViewCount: 0,
              pastConversionCount: 0,
            },
          ],
        },
      });
    });
    expect(screen.getByText("対象記事：Article 2")).toBeInTheDocument();
    expect(screen.queryByTestId("line-chart")).not.toBeInTheDocument();
  });

  it("取得失敗を0件と区別し再試行できる", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi
      .fn()
      .mockRejectedValueOnce(new Error("offline"))
      .mockResolvedValueOnce({ data: { data: [] } });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });
    await user.click(button);
    expect(await screen.findByRole("alert")).toHaveTextContent(
      "集計の取得に失敗しました"
    );
    await user.click(button);
    expect(
      await screen.findByText("適用した条件に該当する集計はありません。")
    ).toBeInTheDocument();
    expect(screen.queryByRole("alert")).not.toBeInTheDocument();
  });

  it("存在しない記事IDのデータは無視される", async () => {
    const user = userEvent.setup();
    mockAxios.post = vi.fn().mockResolvedValue({
      data: {
        data: [
          {
            id: 999, // 存在しない記事ID
            viewCounts: { "20240101": 10 },
            conversionCounts: { "20240101": 5 },
            pastViewCount: 0,
            pastConversionCount: 0,
          },
        ],
      },
    });

    render(<AnalyticsGraph articles={mockArticles} />);
    const button = screen.getByRole("button", { name: "グラフを再表示" });

    await user.click(button);

    await waitFor(() => {
      expect(
        screen.getByText("適用した条件に該当する集計はありません。")
      ).toBeInTheDocument();
    });
  });
});
