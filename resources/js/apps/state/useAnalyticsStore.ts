import { subMonths } from "date-fns";
import { create } from "zustand";

export const useAnalyticsStore = create<Analytics.FilterState>((set) => ({
  start_date: subMonths(new Date(), 1),
  end_date: new Date(),
  type: "daily",
  mode: "sum",
  selected: [],
  set: (partial) => set((state) => ({ ...state, ...partial })),
}));
