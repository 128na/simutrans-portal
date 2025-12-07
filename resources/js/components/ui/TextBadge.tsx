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
        "text-[.7rem] px-1 py-0.5 mr-0.5 text-white font-normal rounded-md bg-c-sub",
        className
      )}
      {...props}
    >
      {children}
    </span>
  );
}
