import React from "react";
import { twMerge } from "tailwind-merge";
import Button from "./Button";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  children: React.ReactNode;
};

export default function ButtonDanger({
  children,
  className,
  ...props
}: ButtonProps) {
  return (
    <Button
      className={twMerge(
        `
        text-sm
        bg-red-600
        hover:bg-red-700
        `,
        className,
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
