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
        bg-brand
        text-white
        rounded-lg
        cursor-pointer
        hover:bg-brand/90
        `,
        className,
      )}
      {...props}
    >
      {children}
    </button>
  );
}
