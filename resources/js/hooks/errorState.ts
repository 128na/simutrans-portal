import { AxiosError } from "axios";
import { useState } from "react";

type ValidationError = {
  errors: Record<string, string[]>;
  message: string;
};

export const useAxiosErrorState = () => {
  const [error, setError] = useState<AxiosError<ValidationError> | null>(null);

  const hasError = (key: string) => {
    const data = error?.response?.data;
    if (!data || !("errors" in data)) {
      return false;
    }
    const errors = (data as ValidationError).errors || {};
    return key in errors;
  };

  const getError = (key: string) => {
    const data = error?.response?.data;
    if (!data || !("errors" in data)) return null;
    const errors = (data as ValidationError).errors || {};
    return errors[key] ?? null;
  };

  return { error, setError, hasError, getError };
};
