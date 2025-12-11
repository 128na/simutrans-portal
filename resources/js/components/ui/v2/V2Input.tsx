import { twMerge } from "tailwind-merge";
type Props = React.InputHTMLAttributes<HTMLInputElement> & {};

export default function V2Input({ className, ...props }: Props) {
  return (
    <>
      <input
        className={twMerge("v2-input", `v2-input-${props.type}`, className)}
        {...props}
      />
      {props.maxLength !== undefined ? (
        <div className="text-right text-sm text-c-sub/70 mt-1">
          {[...String(props.value ?? "")].length} / {props.maxLength}
        </div>
      ) : null}
    </>
  );
}
