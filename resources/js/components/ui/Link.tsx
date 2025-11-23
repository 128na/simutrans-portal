import { twMerge } from "tailwind-merge";
export default function Link({
  children,
  className,
  ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement>) {
  return (
    <a {...props}>
      <span
        className={twMerge(
          "text-gray-700 hover:text-gray-500 decoration-gray-400 underline break-all",
          className
        )}
      >
        {children}
      </span>
    </a>
  );
}
