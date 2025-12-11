import { twMerge } from "tailwind-merge";

type Props = React.InputHTMLAttributes<HTMLInputElement> & {};

export default function Checkbox({ children, className, ...props }: Props) {
  return (
    <label className="inline-block">
      <input
        type="checkbox"
        className={twMerge("v2-checkbox peer", className)}
        {...props}
      />
      <span className="v2-checkbox-label">{children}</span>
    </label>
  );
}
