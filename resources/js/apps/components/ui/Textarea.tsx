import { twMerge } from "tailwind-merge";
import Label from "./Label";
export default function Textarea({
  children,
  className,
  labelClassName,
  ...props
}: React.TextareaHTMLAttributes<HTMLTextAreaElement> & {
  labelClassName?: string;
}) {
  return (
    <Label className={twMerge(labelClassName, children ? "" : "mb-0")}>
      {children}
      <textarea
        className={twMerge(
          "w-full border border-gray-300 rounded-lg px-4 py-2",
          className,
        )}
        {...props}
      />
    </Label>
  );
}
