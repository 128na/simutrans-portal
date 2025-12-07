import { twMerge } from "tailwind-merge";
export default function Label({
  children,
  className,
  ...props
}: React.LabelHTMLAttributes<HTMLLabelElement>) {
  return (
    <label
      className={twMerge(`block text-sm text-c-main`, className)}
      {...props}
    >
      {children}
    </label>
  );
}
