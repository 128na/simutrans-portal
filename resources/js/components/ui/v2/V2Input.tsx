import { twMerge } from "tailwind-merge";
type Props = React.InputHTMLAttributes<HTMLInputElement> & {};

export default function V2Input({ className, ...props }: Props) {
  return (
    <input
      className={twMerge("v2-input", `v2-input-${props.type}`, className)}
      {...props}
    />
  );
}
