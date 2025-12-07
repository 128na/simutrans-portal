import React from "react";
import { twMerge } from "tailwind-merge";
type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export default function TextSub({ children, className, ...props }: HTMLProps) {
  if (children === "") {
    return null;
  }
  return (
    <div
      className={twMerge(
        `
        text-sm text-c-sub break-all
        `,
        className
      )}
      {...props}
    >
      {children}
    </div>
  );
}
