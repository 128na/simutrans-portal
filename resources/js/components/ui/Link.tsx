import { twMerge } from "tailwind-merge";
export default function Link({
  children,
  className,
  ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement>) {
  return (
    <a className={twMerge("v2-link", className)} {...props}>
      {children}
    </a>
  );
}
