import { twMerge } from "tailwind-merge";
import Label from "./Label";
export default function Input({
  children,
  className,
  labelClassName,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & { labelClassName?: string }) {
  return (
    <Label className={labelClassName}>
      {children}
      <input
        className={twMerge(
          "w-full border border-gray-300 rounded-lg px-4 py-2",
          className,
        )}
        {...props}
      />
    </Label>
  );
}
