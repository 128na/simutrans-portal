import React from "react";
import { twMerge } from "tailwind-merge";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: keyof typeof variants;
  size?: keyof typeof sizes;
  children: React.ReactNode;
};

const variants: Record<string, string> = {
  main: "v2-button-main",
  mainOutline: "v2-button-main-outline",
  sub: "v2-button-sub",
  subOutline: "v2-button-sub-outline",
  primary: "v2-button-primary",
  primaryOutline: "v2-button-primary-outline",
  danger: "v2-button-danger",
  dangerOutline: "v2-button-danger-outline",
  warn: "v2-button-warn",
  warnOutline: "v2-button-warn-outline",
  info: "v2-button-info",
  infoOutline: "v2-button-info-outline",
  success: "v2-button-success",
  successOutline: "v2-button-success-outline",
} as const;

const sizes: Record<string, string> = {
  sm: "v2-button-sm",
  md: "v2-button-md",
  lg: "v2-button-lg",
} as const;

export default function V2Button({
  children,
  className,
  variant = "primary",
  size = "md",
  ...props
}: ButtonProps) {
  return (
    <button
      type="button"
      className={twMerge(
        variant.includes("Outline") ? "v2-button-outline" : "v2-button",
        variants[variant],
        sizes[size],
        props.disabled && "v2-button-disabled",
        className
      )}
      {...props}
    >
      {children}
    </button>
  );
}
