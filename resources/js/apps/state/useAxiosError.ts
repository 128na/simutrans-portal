import { create } from "zustand";

type AxiosError = {
  errors: Record<string, string[]>;
  message: string | null;
  setError: (data: {
    errors: Record<string, string[]>;
    message: string | null;
  }) => void;
  clearError: () => void;
  hasError: (key: string) => boolean;
  getError: (key: string) => string | null;
};

export const useAxiosError = create<AxiosError>((set, get) => ({
  errors: {},
  message: null,
  setError: ({ errors, message }) => set({ errors, message }),
  clearError: () => set({ errors: {}, message: null }),
  hasError: (key) => key in get().errors,
  getError: (key) => get().errors[key]?.join("、") ?? null,
}));
