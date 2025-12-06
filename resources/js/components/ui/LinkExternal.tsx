import { twMerge } from "tailwind-merge";
export default function LinkExternal({
  children,
  className,
  ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement>) {
  return (
    <a {...props}>
      <span className={twMerge("link-external", className)}>{children}</span>
      <span className="text-xs text-muted">↗</span>
    </a>
  );
}
