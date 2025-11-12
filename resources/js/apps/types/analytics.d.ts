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
    mode: Mode;
    selected: number[];
    set: (partial: Partial<FilterState>) => void;
  };

  type Type = "daily" | "monthly" | "yearly";
  type Mode = "line" | "sum";
}
