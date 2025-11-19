import React from "react";
import { twMerge } from "tailwind-merge";
type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
  color?: null | "red" | "green";
};

export default function TextBadge({
  children,
  className,
  color,
  ...props
}: HTMLProps) {
  if (children === "") {
    return null;
  }
  return (
    <span
      className={twMerge(
        `text-xs p-0.5 mr-0.5 text-white font-normal rounded-md bg-gray-500`,
        color && `bg-${color}-500`,
        className,
      )}
      {...props}
    >
      {children}
    </span>
  );
}
