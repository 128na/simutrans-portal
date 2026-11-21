import { create } from "zustand";
import { isValidationError } from "@/lib/errorHandler";

type ValidationErrorState = {
  errors: Record<string, string[]>;
  message: string | null;
  setError: (data: {
    errors: Record<string, string[]>;
    message: string | null;
  }) => void;
  /**
   * バリデーションエラー（422）の場合にストアへセットし true を返す。
   * それ以外のエラーは無視して false を返す。
   * catch ブロックで isValidationError + setError を置き換えるために使う。
   */
  setValidationErrorFrom: (error: unknown) => boolean;
  clearError: () => void;
  hasError: (key: string) => boolean;
  getError: (key: string) => string | null;
  /** key そのものと、配下のキー（key.0 など）にエラーがあるか */
  hasErrorTree: (key: string) => boolean;
  /** key そのものと、配下のキー（key.0 など）のメッセージをまとめて返す */
  getErrorTree: (key: string) => string | null;
};

const isInTree = (errorKey: string, key: string) =>
  errorKey === key || errorKey.startsWith(key + ".");

export const useAxiosError = create<ValidationErrorState>((set, get) => ({
  errors: {},
  message: null,
  setError: ({ errors, message }) => set({ errors, message }),
  setValidationErrorFrom: (error: unknown): boolean => {
    if (isValidationError(error)) {
      set({
        errors: error.response.data.errors,
        message: error.response.data.message,
      });
      return true;
    }
    return false;
  },
  clearError: () => set({ errors: {}, message: null }),
  hasError: (key) => key in get().errors,
  getError: (key) => get().errors[key]?.join("、") ?? null,
  hasErrorTree: (key) =>
    Object.keys(get().errors).some((k) => isInTree(k, key)),
  getErrorTree: (key) => {
    const messages = Object.entries(get().errors)
      .filter(([k]) => isInTree(k, key))
      .flatMap(([, v]) => v);
    return messages.length ? [...new Set(messages)].join("、") : null;
  },
}));
