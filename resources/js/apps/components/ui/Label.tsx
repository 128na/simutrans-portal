import { twMerge } from "tailwind-merge";
export default function Label({
  children,
  className,
  ...props
}: React.LabelHTMLAttributes<HTMLLabelElement>) {
  return (
    <label
      className={twMerge(
        `block text-sm text-gray-900`,
        children ? "mb-2" : "",
        className,
      )}
      {...props}
    >
      {children}
    </label>
  );
}
