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
  return (
    <Label className={twMerge(labelClassName, children ? "" : "mb-0")}>
      {children}
      <select
        value={value} // ← ここが重要！
        onChange={onChange} // ← props経由で受け取る
        className={twMerge(
          "w-full border border-gray-300 rounded-lg px-4 py-2",
          className,
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
