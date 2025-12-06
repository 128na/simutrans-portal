import { twMerge } from "tailwind-merge";
export default function Link({
  children,
  className,
  ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement>) {
  return (
    <a {...props}>
      <span className={twMerge("link-internal", className)}>{children}</span>
    </a>
  );
}
