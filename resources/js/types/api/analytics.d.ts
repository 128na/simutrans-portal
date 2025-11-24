export declare namespace Analytics {
  type Article = {
    id: number;
    title: string;
    published_at: string;
    modified_at: string;
  };

  type FilterState = {
    start_date: Date;
    end_date: Date;
    type: Type;
    axes: Axis[];
    mode: Mode;
    selected: number[];
    set: (partial: Partial<FilterState>) => void;
  };
  type Type = "daily" | "monthly" | "yearly";
  type Axis = "cv" | "pv";
  type Mode = "periodic" | "cumulative";
  type Period = string; // Date period string (e.g., "2024-01", "2024-01-15")

  type ArticleAnalytic = {
    id: number;
    viewCounts: Record<Period, number>;
    conversionCounts: Record<Period, number>;
    pastViewCount: number;
    pastConversionCount: number;
  };
}
