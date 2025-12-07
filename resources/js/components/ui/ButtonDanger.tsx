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
    <Button className={twMerge("button-danger", className)} {...props}>
      {children}
    </Button>
  );
}
