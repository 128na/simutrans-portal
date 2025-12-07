import React from "react";
import { twMerge } from "tailwind-merge";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: keyof typeof variants;
  children: React.ReactNode;
};

const variants: Record<string, string> = {
  primary: "bg-primary text-white hover:bg-primary/80",
  primaryOutline:
    "bg-transparent border border-primary text-primary hover:bg-primary/10",
  danger: "bg-danger text-white hover:bg-danger/80",
  dangerOutline:
    "bg-transparent border border-danger text-danger hover:bg-danger/10",
  warn: "bg-warn text-white hover:bg-warn/80",
  warnOutline: "bg-transparent border border-warn text-warn hover:bg-warn/10",
  info: "bg-info text-white hover:bg-info/80",
  infoOutline: "bg-transparent border border-info text-info hover:bg-info/10",
  success: "bg-success text-white hover:bg-success/80",
  successOutline:
    "bg-transparent border border-success text-success hover:bg-success/10",
  secondary: "bg-secondary text-white hover:bg-secondary/80",
  secondaryOutline:
    "bg-transparent border border-secondary text-secondary hover:bg-secondary/10",
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
