import { useRef } from "react";
import { twMerge } from "tailwind-merge";
import Button from "./Button";

export default function InputFile({
  className,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement>) {
  const fileRef = useRef<HTMLInputElement>(null);

  return (
    <div>
      <Button
        onClick={() => {
          fileRef.current?.click();
        }}
      >
        アップロード
      </Button>

      <input
        ref={fileRef}
        type="file"
        className={twMerge("hidden", className)}
        {...props}
      />
    </div>
  );
}
