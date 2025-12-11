import { twMerge } from "tailwind-merge";
type Props = React.TextareaHTMLAttributes<HTMLTextAreaElement> & {};

export default function Textarea({ className, ...props }: Props) {
  return (
    <>
      <textarea className={twMerge("v2-input", className)} {...props} />
      {props.maxLength !== undefined ? (
        <div className="text-right text-sm text-c-sub/70 mt-1">
          {[...String(props.value ?? "")].length} / {props.maxLength}
        </div>
      ) : null}
    </>
  );
}
