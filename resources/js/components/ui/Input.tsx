import { twMerge } from "tailwind-merge";
import Label from "./Label";
export default function Input({
  children,
  className,
  labelClassName,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & { labelClassName?: string }) {
  const hasChildren = Array.isArray(children)
    ? children.length > 0
    : !!children;
  return (
    <Label className={labelClassName}>
      {children}
      <input
        className={twMerge(
          "w-full border border-gray-300 rounded-lg px-4 py-2 invalid:border-red-500 invalid:bg-red-100",
          hasChildren ? "mt-1" : "mb-0",
          className
        )}
        {...props}
      />
    </Label>
  );
}
