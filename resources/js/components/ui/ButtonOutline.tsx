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
        "px-3 py-1.5 text-sm border border-gray-400 text-muted bg-white hover:bg-gray-400 hover:border-gray-400 hover:text-white"
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
