import { twMerge } from "tailwind-merge";
export default function LinkExternal({
  children,
  className,
  ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement>) {
  return (
    <a {...props}>
      <span
        className={twMerge(
          "decoration-sky-600 text-sky-600 underline break-all",
          className,
        )}
      >
        {children}
      </span>
      <span className="text-sm text-gray-500">↗</span>
    </a>
  );
}
