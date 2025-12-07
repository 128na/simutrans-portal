import React from "react";
import { twMerge } from "tailwind-merge";
type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  children: React.ReactNode;
};

export default function Button({ children, className, ...props }: ButtonProps) {
  return (
    <button
      type="button"
      className={twMerge(
        `
        px-4 py-2
        bg-c-primary
        text-md
        text-white
        rounded-lg
        cursor-pointer
        hover:bg-c-primary/80
        disabled:bg-c-sub
        disabled:cursor-not-allowed
        `,
        className
      )}
      {...props}
    >
      {children}
    </button>
  );
}
