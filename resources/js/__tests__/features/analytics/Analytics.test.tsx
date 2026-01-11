import { describe, it, expect, vi, beforeEach } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { Analytics } from "@/features/analytics/Analytics";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";

describe("Analytics Integration Component", () => {
  const mockArticles: Analytics.Article[] = [
    {
      id: 1,
      title: "Test Article 1",
      published_at: "2025-12-01",
      modified_at: "2026-01-01",
      total_view_count: { count: 150 },
      total_conversion_count: { count: 10 },
    },
    {
      id: 2,
      title: "Test Article 2",
      published_at: "2025-11-15",
      modified_at: "2026-01-05",
      total_view_count: { count: 250 },
      total_conversion_count: { count: 20 },
    },
    {
      id: 3,
      title: "Test Article 3",
      published_at: "2025-10-01",
      modified_at: "2026-01-03",
      total_view_count: null,
      total_conversion_count: null,
    },
  ];

  beforeEach(() => {
    vi.clearAllMocks();
    // Reset Zustand store to initial state
    useAnalyticsStore.setState(() => ({
      start_date: new Date(Date.now() - 90 * 24 * 60 * 60 * 1000), // 3 months ago
      end_date: new Date(),
      type: "daily",
      axes: ["cv", "pv"],
      selected: [],
      mode: "periodic",
    }));
  });

  it("レンダリング: 全コンポーネント表示", () => {
    render(<Analytics articles={mockArticles} />);

    expect(screen.getByText("期間プリセット")).toBeInTheDocument();
    expect(screen.getByText("表示条件")).toBeInTheDocument();
    expect(screen.getByText("表示記事")).toBeInTheDocument();
  });

  it("グラフ表示: グラフセクション表示", () => {
    render(<Analytics articles={mockArticles} />);

    // Check if graph section exists
    const graphSections = screen.getAllByText("グラフ");
    expect(graphSections.length).toBeGreaterThan(0);
  });

  it("期間プリセット: 3ヵ月ボタン操作", async () => {
    const user = userEvent.setup();
    render(<Analytics articles={mockArticles} />);

    const threeMonthButton = screen.getByRole("button", { name: "3ヵ月" });
    expect(threeMonthButton).toBeInTheDocument();

    await user.click(threeMonthButton);

    // Button should still be present after click
    expect(threeMonthButton).toBeInTheDocument();
  });

  it("期間プリセット: 12ヵ月ボタン操作", async () => {
    const user = userEvent.setup();
    render(<Analytics articles={mockArticles} />);

    const twelveMonthButton = screen.getByRole("button", { name: "12ヵ月" });
    expect(twelveMonthButton).toBeInTheDocument();

    await user.click(twelveMonthButton);

    expect(twelveMonthButton).toBeInTheDocument();
  });

  it("期間プリセット: 3年ボタン操作", async () => {
    const user = userEvent.setup();
    render(<Analytics articles={mockArticles} />);

    const threeYearButton = screen.getByRole("button", { name: "3年" });
    expect(threeYearButton).toBeInTheDocument();

    await user.click(threeYearButton);

    expect(threeYearButton).toBeInTheDocument();
  });

  it("日付入力: 開始日が存在", () => {
    render(<Analytics articles={mockArticles} />);

    const startDateInput = screen.getByLabelText("開始日") as HTMLInputElement;
    expect(startDateInput).toBeInTheDocument();
    expect(startDateInput.type).toBe("date");
  });

  it("日付入力: 終了日が存在", () => {
    render(<Analytics articles={mockArticles} />);

    const endDateInput = screen.getByLabelText("終了日") as HTMLInputElement;
    expect(endDateInput).toBeInTheDocument();
    expect(endDateInput.type).toBe("date");
  });

  it("表示間隔: 間隔セレクト要素", () => {
    render(<Analytics articles={mockArticles} />);

    const typeSelect = screen.getByLabelText("間隔") as HTMLSelectElement;
    expect(typeSelect).toBeInTheDocument();
    expect(typeSelect.value).toBe("daily");
  });

  it("表示間隔: オプション操作確認", () => {
    render(<Analytics articles={mockArticles} />);

    const typeSelect = screen.getByLabelText("間隔") as HTMLSelectElement;
    expect(typeSelect.querySelectorAll("option").length).toBeGreaterThanOrEqual(
      3
    );
  });

  it("表示間隔: 年次オプション確認", () => {
    render(<Analytics articles={mockArticles} />);

    const typeSelect = screen.getByLabelText("間隔") as HTMLSelectElement;
    const yearlyOption = Array.from(typeSelect.options).find(
      (option) => option.value === "yearly"
    );
    expect(yearlyOption).toBeDefined();
    expect(yearlyOption?.textContent).toBe("年次");
  });

  it("モード: モードセレクト要素", () => {
    render(<Analytics articles={mockArticles} />);

    const modeSelect = screen.getByLabelText("モード") as HTMLSelectElement;
    expect(modeSelect).toBeInTheDocument();
    expect(modeSelect.value).toBe("periodic");
  });

  it("表示データ: PV チェックボックス存在", () => {
    render(<Analytics articles={mockArticles} />);

    const pvCheckbox = screen.getByRole("checkbox", {
      name: /ページ表示回数/i,
    });
    expect(pvCheckbox).toBeInTheDocument();
  });

  it("表示データ: CV チェックボックス存在", () => {
    render(<Analytics articles={mockArticles} />);

    const cvCheckbox = screen.getByRole("checkbox", {
      name: /DL・リンククリック回数/i,
    });
    expect(cvCheckbox).toBeInTheDocument();
  });

  it("表示記事: 検索フォーム存在", () => {
    render(<Analytics articles={mockArticles} />);

    const searchInput = screen.getByPlaceholderText("検索");
    expect(searchInput).toBeInTheDocument();
  });

  it("記事選択: テーブル行表示", () => {
    render(<Analytics articles={mockArticles} />);

    // Table should display article rows
    expect(screen.getByText("Test Article 1")).toBeInTheDocument();
    expect(screen.getByText("Test Article 2")).toBeInTheDocument();
  });

  it("統合: 複数セクション同時表示", () => {
    render(<Analytics articles={mockArticles} />);

    // All major sections should be present
    expect(screen.getByText("期間プリセット")).toBeInTheDocument();
    expect(screen.getByText("表示条件")).toBeInTheDocument();
    expect(screen.getByText("表示データ")).toBeInTheDocument();
    expect(screen.getByText("表示記事")).toBeInTheDocument();
  });

  it("エラーハンドリング: データなし対応", () => {
    const emptyArticles: Analytics.Article[] = [];
    render(<Analytics articles={emptyArticles} />);

    // Should still render controls
    expect(screen.getByText("期間プリセット")).toBeInTheDocument();
  });

  it("記事表示: タイトル列確認", () => {
    render(<Analytics articles={mockArticles} />);

    const headerCells = screen.getAllByRole("columnheader");
    const titleHeader = headerCells.find((cell) =>
      cell.textContent?.includes("タイトル")
    );
    expect(titleHeader).toBeInTheDocument();
  });

  it("記事表示: PV数列確認", () => {
    render(<Analytics articles={mockArticles} />);

    const headerCells = screen.getAllByRole("columnheader");
    const pvHeader = headerCells.find((cell) =>
      cell.textContent?.includes("PV数")
    );
    expect(pvHeader).toBeInTheDocument();
  });

  it("記事表示: CV数列確認", () => {
    render(<Analytics articles={mockArticles} />);

    const headerCells = screen.getAllByRole("columnheader");
    const cvHeader = headerCells.find((cell) =>
      cell.textContent?.includes("CV数")
    );
    expect(cvHeader).toBeInTheDocument();
  });
});
