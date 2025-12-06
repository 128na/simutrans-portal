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
  const hasChildren = Array.isArray(children)
    ? children.length > 0
    : !!children;
  return (
    <Label className={labelClassName}>
      {children}
      <textarea
        className={twMerge(
          "w-full border border-g2 rounded-lg px-4 py-2",
          hasChildren ? "mt-1" : "mb-0",
          className
        )}
        {...props}
      />
    </Label>
  );
}
