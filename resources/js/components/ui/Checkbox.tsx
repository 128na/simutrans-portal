import { twMerge } from "tailwind-merge";
import Label from "./Label";
export default function Checkbox({
  children,
  className,
  labelClassName,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & {
  labelClassName?: string;
}) {
  return (
    <Label
      className={twMerge(
        "sm:mr-2 mr-4 sm:mb-1 mb-2 inline-block text-sm text-g9 cursor-pointer",
        labelClassName
      )}
    >
      <input
        type="checkbox"
        className={twMerge("mr-0.5 accent-brand", className)}
        {...props}
      />
      {children}
    </Label>
  );
}
