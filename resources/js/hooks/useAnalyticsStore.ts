import { subDays } from "date-fns";
import { create } from "zustand";

export const useAnalyticsStore = create<Analytics.FilterState>((set) => {
  const today = new Date();
  return {
    start_date: subDays(today, 6),
    end_date: today,
    type: "daily",
    axes: ["cv", "pv"],
    selected: [],
    mode: "periodic",
    set: (partial) => set((state) => ({ ...state, ...partial })),
  };
});
