import React from "react";
import { twMerge } from "tailwind-merge";
import Button from "@/components/ui/Button";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  children: React.ReactNode;
};

export default function ButtonSub({
  children,
  className,
  ...props
}: ButtonProps) {
  return (
    <Button
      type="button"
      className={twMerge(
        className,
        "px-3 py-1.5 text-sm text-white bg-c-sub/70 hover:bg-c-sub/90"
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
