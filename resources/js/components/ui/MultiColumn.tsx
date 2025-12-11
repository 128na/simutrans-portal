import React from "react";
import { twMerge } from "tailwind-merge";

type MultiColumnProps = {
  children: React.ReactNode[];
  classNames?: string[];
} & React.HTMLAttributes<HTMLElement>;

export default function MultiColumn({
  children,
  classNames = [],
  className,
  ...props
}: MultiColumnProps) {
  return (
    <div className={twMerge("flex w-full gap-4", className)} {...props}>
      {children.map((child, index) => (
        <div key={index} className={classNames[index] || ""}>
          {child}
        </div>
      ))}
    </div>
  );
}
