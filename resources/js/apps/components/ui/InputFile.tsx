import { twMerge } from "tailwind-merge";

export default function InputFile({
  className,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement>) {
  return (
    <input
      type="file"
      className={twMerge(
        "block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50",
        className,
      )}
      {...props}
    />
  );
}
