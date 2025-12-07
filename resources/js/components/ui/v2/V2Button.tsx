import React from "react";
import { twMerge } from "tailwind-merge";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: keyof typeof variants;
  children: React.ReactNode;
};

const variants: Record<string, string> = {
  primary: "bg-c-primary text-white hover:bg-c-primary/80",
  primaryOutline:
    "bg-transparent border border-c-primary text-c-primary hover:bg-c-primary/10",
  danger: "bg-c-danger text-white hover:bg-c-danger/80",
  dangerOutline:
    "bg-transparent border border-c-danger text-c-danger hover:bg-c-danger/10",
  warn: "bg-c-warn text-white hover:bg-c-warn/80",
  warnOutline:
    "bg-transparent border border-c-warn text-c-warn hover:bg-c-warn/10",
  info: "bg-c-info text-white hover:bg-c-info/80",
  infoOutline:
    "bg-transparent border border-c-info text-c-info hover:bg-c-info/10",
  success: "bg-c-success text-white hover:bg-c-success/80",
  successOutline:
    "bg-transparent border border-c-success text-c-success hover:bg-c-success/10",
  secondary: "bg-c-secondary text-white hover:bg-c-secondary/80",
  secondaryOutline:
    "bg-transparent border border-c-secondary text-c-secondary hover:bg-c-secondary/10",
} as const;

export default function V2Button({
  children,
  className,
  variant = "primary",
  ...props
}: ButtonProps) {
  return (
    <button
      type="button"
      className={twMerge(
        "px-4 py-2 rounded-lg",
        variants[variant],
        props.disabled
          ? "cursor-not-allowed opacity-50 hover:bg-none"
          : "cursor-pointer",
        className
      )}
      {...props}
    >
      {children}
    </button>
  );
}
