import { twMerge } from "tailwind-merge";
type Props = React.TextareaHTMLAttributes<HTMLTextAreaElement> & {};

export default function V2Textarea({ className, ...props }: Props) {
  return <textarea className={twMerge("v2-input", className)} {...props} />;
}
