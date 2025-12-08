import React from "react";
import { twMerge } from "tailwind-merge";
type Props = React.ButtonHTMLAttributes<HTMLElement> & {
  variant?: keyof typeof variants;
};

const variants: Record<string, string> = {
  main: "v2-card-main",
  sub: "v2-card-sub",
  primary: "v2-card-primary",
  danger: "v2-card-danger",
  warn: "v2-card-warn",
  info: "v2-card-info",
  success: "v2-card-success",
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
        "v2-card",
        variant in variants ? variants[variant] : variants.sub,
        className
      )}
      {...props}
    >
      {children}
    </div>
  );
}
