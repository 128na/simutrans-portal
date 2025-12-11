import { twMerge } from "tailwind-merge";
type Props = React.InputHTMLAttributes<HTMLInputElement> & {
  counter?: (value: string) => number;
};

export default function V2Input({ counter, className, ...props }: Props) {
  const count =
    counter && typeof props.value === "string"
      ? counter(props.value ?? "")
      : [...String(props.value ?? "")].length;

  return (
    <>
      <input
        className={twMerge("v2-input", `v2-input-${props.type}`, className)}
        {...props}
      />
      {props.maxLength !== undefined ? (
        <div className="text-right text-sm text-c-sub/70 mt-1">
          {count} / {props.maxLength}
        </div>
      ) : null}
    </>
  );
}
