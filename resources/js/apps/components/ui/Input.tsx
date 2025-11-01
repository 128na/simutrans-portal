import { twMerge } from "tailwind-merge";
export default function Input({
  children,
  className,
  labelClassName,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & { labelClassName?: string }) {
  return (
    <label
      className={twMerge(
        `block text-sm text-gray-900`,
        children ? "mb-2" : "",
        labelClassName,
      )}
    >
      {children}
      <input
        className={twMerge(
          "w-full border border-gray-300 rounded-lg px-4 py-2",
          className,
        )}
        {...props}
      />
    </label>
  );
}
