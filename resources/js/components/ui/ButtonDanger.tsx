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
        bg-c-danger
        hover:bg-c-danger/80
        `,
        className
      )}
      {...props}
    >
      {children}
    </Button>
  );
}
