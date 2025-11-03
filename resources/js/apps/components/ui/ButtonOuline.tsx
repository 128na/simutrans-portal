import React from "react";
import { twMerge } from "tailwind-merge";
import Button from "@/apps/components/ui/Button";
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
        "border border-gray-500 text-gray-500 bg-white hover:bg-gray-100",
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
