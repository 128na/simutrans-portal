import { twMerge } from "tailwind-merge";
export default function Link({
  children,
  className,
  ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement>) {
  const external =
    props.href &&
    !props.href.startsWith(window.location.origin) &&
    !props.href.startsWith("/") &&
    !props.href.startsWith("#");

  if (external) {
    props.target = "_blank";
    props.rel = "noopener noreferrer";
  }
  return (
    <a className={twMerge("v2-link", className)} {...props}>
      {children}
    </a>
  );
}
