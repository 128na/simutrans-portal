import React from "react";
import { twMerge } from "tailwind-merge";
type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export default function TextError({
  children,
  className,
  ...props
}: HTMLProps) {
  if (children === "") {
    return null;
  }
  return (
    <div className={twMerge("text-sm text-red-600", className)} {...props}>
      {children}
    </div>
  );
}
