import { twMerge } from "tailwind-merge";
import Label from "./Label";
export default function Input({
  children,
  className,
  labelClassName,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & { labelClassName?: string }) {
  return (
    <Label className={twMerge(labelClassName, children ? "" : "mb-0")}>
      {children}
      <input
        className={twMerge(
          "w-full border border-gray-300 rounded-lg px-4 py-2 invalid:border-red-500 invalid:bg-red-100",
          className,
        )}
        {...props}
      />
    </Label>
  );
}
