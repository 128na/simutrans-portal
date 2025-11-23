import { useRef } from "react";
import { twMerge } from "tailwind-merge";
import ButtonSub from "./ButtonSub";

export default function InputFile({
  className,
  children,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement>) {
  const fileRef = useRef<HTMLInputElement>(null);

  return (
    <>
      <ButtonSub
        onClick={() => {
          fileRef.current?.click();
        }}
      >
        {children ?? "アップロード"}
      </ButtonSub>

      <input
        ref={fileRef}
        type="file"
        className={twMerge("hidden", className)}
        {...props}
      />
    </>
  );
}
