import { subMonths } from "date-fns";
import { create } from "zustand";

export const useAnalyticsStore = create<Analytics.FilterState>((set) => ({
  start_date: subMonths(new Date(), 3),
  end_date: new Date(),
  type: "daily",
  axes: ["cv", "pv"],
  selected: [],
  mode: "periodic",
  set: (partial) => set((state) => ({ ...state, ...partial })),
}));
