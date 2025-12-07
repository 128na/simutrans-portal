import React from "react";
import { twMerge } from "tailwind-merge";
type Props = React.ButtonHTMLAttributes<HTMLElement> & {
  variant?: keyof typeof variants;
  children: React.ReactNode;
};

const variants: Record<string, string> = {
  primary: "bg-transparent border border-c-primary text-c-primary",
  danger: "bg-transparent border border-c-danger text-c-danger",
  warn: "bg-transparent border border-c-warn text-c-warn",
  info: "bg-transparent border border-c-info text-c-info",
  success: "bg-transparent border border-c-success text-c-success",
  secondary: "bg-transparent border border-c-secondary text-c-secondary",
} as const;

export default function V2Card({
  children,
  className,
  variant = "primary",
  ...props
}: Props) {
  return (
    <div
      className={twMerge(
        "p-4 rounded-lg block w-full text-left",
        variant in variants ? variants[variant] : variants.secondary,
        className
      )}
      {...props}
    >
      {children}
    </div>
  );
}
