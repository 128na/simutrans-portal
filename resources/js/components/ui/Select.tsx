import { twMerge } from "tailwind-merge";
import Label from "./Label";

export default function Select({
  children,
  className,
  labelClassName,
  options,
  value,
  onChange,
  ...props
}: React.SelectHTMLAttributes<HTMLSelectElement> & {
  labelClassName?: string;
  options: Record<string, string>;
}) {
  const hasChildren = Array.isArray(children)
    ? children.length > 0
    : !!children;
  return (
    <Label className={labelClassName}>
      {children}
      <select
        value={value}
        onChange={onChange}
        className={twMerge(
          "w-full border border-muted rounded-lg px-4 py-2",
          hasChildren ? "mt-1" : "mb-0",
          className
        )}
        {...props}
      >
        {Object.entries(options).map(([key, label]) => (
          <option key={key} value={key}>
            {label}
          </option>
        ))}
      </select>
    </Label>
  );
}
