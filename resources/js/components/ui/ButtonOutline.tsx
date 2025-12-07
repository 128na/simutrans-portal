import React from "react";
import { twMerge } from "tailwind-merge";
import Button from "@/components/ui/Button";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  children: React.ReactNode;
};

export default function ButtonOutline({
  children,
  className,
  ...props
}: ButtonProps) {
  return (
    <Button
      type="button"
      className={twMerge(
        className,
        "px-3 py-1.5 text-sm border border-c-sub/10 text-c-sub bg-c-sub/20 hover:bg-c-sub/40 disabled:bg-c-sub/10"
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
