import { useRef } from "react";
import axios from "axios";
import { handleError } from "@/lib/errorHandler";
import Button from "../ui/Button";

type UploadCallbacks = {
  onUploaded?: (attachment: Attachment.MypageEdit) => void;
  onUploadingChange?: (uploading: boolean) => void;
  onUploadError?: (error: unknown) => void;
};

type Prop = {
  /** ボタンに出す文言 */
  label?: string;
} & UploadCallbacks &
  Omit<React.InputHTMLAttributes<HTMLInputElement>, "type">;

/**
 * 1 ファイルをアップロードする関数を返す。ボタンからの選択とドロップの両方がこの経路を通る。
 */
export const useUploadFile = ({
  onUploaded,
  onUploadingChange,
  onUploadError,
}: UploadCallbacks) => {
  return async (file: File): Promise<void> => {
    const formData = new FormData();
    formData.append("file", file);

    try {
      onUploadingChange?.(true);
      const response = await axios.post("/api/v2/attachments", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      onUploaded?.(response.data.data);
    } catch (error) {
      onUploadError?.(error);
      handleError(error, { component: "Upload", action: "upload" });
    } finally {
      onUploadingChange?.(false);
    }
  };
};

/**
 * ファイルのアップロードボタン。
 * ブラウザ標準のファイル入力は「選択されていません」と表示され、設定済みのファイルが無いと誤解されるため隠し、
 * 代わりにボタンからファイル選択を開く。className はボタンに付く。
 */
export const Upload = ({
  label = "新しいファイルをアップロード",
  onUploaded,
  onUploadingChange,
  onUploadError,
  className,
  disabled,
  ...props
}: Prop) => {
  const inputRef = useRef<HTMLInputElement>(null);
  const uploadFile = useUploadFile({
    onUploaded,
    onUploadingChange,
    onUploadError,
  });

  const onUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const input = e.target;
    const file = input.files?.[0];
    if (!file) return;

    try {
      await uploadFile(file);
    } finally {
      // 同じファイルを選び直しても change が起きるように空にする
      input.value = "";
    }
  };

  return (
    <>
      <Button
        variant="sub"
        className={className}
        disabled={disabled}
        onClick={() => inputRef.current?.click()}
      >
        {label}
      </Button>
      <input
        ref={inputRef}
        type="file"
        hidden
        tabIndex={-1}
        aria-hidden="true"
        onChange={onUpload}
        multiple={false}
        disabled={disabled}
        {...props}
      />
    </>
  );
};
