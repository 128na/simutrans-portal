import React from "react";
import { twMerge } from "tailwind-merge";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  children: React.ReactNode;
};

export default function Button({ children, className, ...props }: ButtonProps) {
  return (
    <button
      type="button"
      className={twMerge("button-primary", className)}
      {...props}
    >
      {children}
    </button>
  );
}
