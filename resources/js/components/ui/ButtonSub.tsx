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
      className={twMerge(className, "button-sub")}
      {...props}
    >
      {children}
    </Button>
  );
}
