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
          "underline decoration-gray-400 break-all",
          className,
        )}
      >
        {children}
      </span>
    </a>
  );
}
