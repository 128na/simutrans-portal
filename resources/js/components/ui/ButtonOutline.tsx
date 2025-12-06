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
        "px-3 py-1.5 text-sm border border-g2 text-g4 bg-g2/50 hover:bg-g4 hover:text-white disabled:bg-g1"
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
