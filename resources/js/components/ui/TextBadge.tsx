import React from "react";
import { twMerge } from "tailwind-merge";
type HTMLProps = React.HTMLAttributes<HTMLElement>;

export default function TextBadge({
  children,
  className,
  ...props
}: HTMLProps) {
  if (children === "") {
    return null;
  }
  return (
    <span
      className={twMerge(
        "text-[.75rem] p-0.5 mr-0.5 text-white font-normal rounded-md bg-gray-500",
        className
      )}
      {...props}
    >
      {children}
    </span>
  );
}
