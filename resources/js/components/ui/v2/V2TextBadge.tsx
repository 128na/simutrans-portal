import React from "react";
import { twMerge } from "tailwind-merge";
type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  variant?: keyof typeof variants;
};

const variants: Record<string, string> = {
  main: "v2-badge-main",
  sub: "v2-badge-sub",
  primary: "v2-badge-primary",
  danger: "v2-badge-danger",
  warn: "v2-badge-warn",
  info: "v2-badge-info",
  success: "v2-badge-success",
} as const;

export default function V2TextBadge({
  children,
  className,
  variant = "sub",
  ...props
}: HTMLProps) {
  if (children === "") {
    return null;
  }
  return (
    <span
      className={twMerge(
        "v2-badge",
        variant in variants ? variants[variant] : variants.sub,
        className
      )}
      {...props}
    >
      {children}
    </span>
  );
}
