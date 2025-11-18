import React from "react";
import { twMerge } from "tailwind-merge";
import Button from "@/apps/components/ui/Button";
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
      className={twMerge(className, "text-white bg-gray-500 hover:bg-gray-600")}
      {...props}
    >
      {children}
    </Button>
  );
}
