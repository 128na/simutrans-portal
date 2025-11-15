namespace Analytics {
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
    selected: number[];
    set: (partial: Partial<FilterState>) => void;
  };
  type Type = "daily" | "monthly" | "yearly";
  type Axis = "cv" | "pv";

  type ArticleAnalytic = [
    ArtcileId,
    Record<Period, Count>,
    Record<Period, Count>,
  ];
  type ArtcileId = number;
  type Period = number;
  type Count = number;
}
