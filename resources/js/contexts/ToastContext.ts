import { createContext } from "react";

export interface Toast {
  id: string;
  message: string;
  variant: "success" | "error" | "warning" | "info";
  duration: number;
  createdAt: number;
}

export interface ToastContextType {
  toasts: Toast[];
  show: (
    message: string,
    variant: "success" | "error" | "warning" | "info",
    duration: number
  ) => string;
  dismiss: (id: string) => void;
  dismissAll: () => void;
}

export const ToastContext = createContext<ToastContextType | undefined>(
  undefined
);
