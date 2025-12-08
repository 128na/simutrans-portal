import { twMerge } from "tailwind-merge";

type Props = React.InputHTMLAttributes<HTMLInputElement> & { id: string };

export default function V2Checkbox({
  children,
  className,
  id,
  ...props
}: Props) {
  return (
    <div className="inline-block">
      <input
        id={`checkbox-${id}`}
        type="checkbox"
        className={twMerge("v2-checkbox", className)}
        {...props}
      />
      <label
        htmlFor={`checkbox-${id}`}
        className={twMerge(
          "v2-checkbox-label",
          props.disabled && "v2-checkbox-label-disabled",
          props.checked && "v2-checkbox-label-checked",
          props.required && "v2-checkbox-label-required"
        )}
      >
        {children}
      </label>
    </div>
  );
}
