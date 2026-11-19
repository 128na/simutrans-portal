import { useRef, useState } from "react";
import axios from "axios";
import { handleError } from "@/lib/errorHandler";
import Button from "../ui/Button";

type UploadCallbacks = {
  onUploaded?: (attachment: Attachment.MypageEdit) => void;
  onUploadingChange?: (uploading: boolean) => void;
  onUploadError?: (error: unknown) => void;
};

export type FileUploader = {
  /** 1 ファイルをアップロードする。アップロード中に呼ばれたときは何もしない */
  uploadFile: (file: File) => Promise<void>;
  /** アップロード中か */
  uploading: boolean;
};

type Prop = {
  /** ボタンに出す文言 */
  label?: string;
  /** ドロップなど他の経路と状態を共有するときに渡す。無ければ自前で持つ */
  uploader?: FileUploader;
} & UploadCallbacks &
  Omit<React.InputHTMLAttributes<HTMLInputElement>, "type">;

/**
 * 1 ファイルをアップロードする関数とアップロード中かを返す。ボタンからの選択とドロップの両方がこの経路を通る。
 * 同時に 2 つ走ると後から終わったほうが選ばれてしまうので、アップロード中に来たものは受け付けない。
 */
export const useUploadFile = ({
  onUploaded,
  onUploadingChange,
  onUploadError,
}: UploadCallbacks): FileUploader => {
  // 同じタイミングで続けて呼ばれても state はまだ変わっていないので、判定は ref で行う
  const uploadingRef = useRef(false);
  const [uploading, setUploading] = useState(false);

  const uploadFile = async (file: File): Promise<void> => {
    if (uploadingRef.current) return;
    uploadingRef.current = true;
    setUploading(true);

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
      uploadingRef.current = false;
      setUploading(false);
      onUploadingChange?.(false);
    }
  };

  return { uploadFile, uploading };
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
  uploader,
  ...props
}: Prop) => {
  const inputRef = useRef<HTMLInputElement>(null);
  const ownUploader = useUploadFile({
    onUploaded,
    onUploadingChange,
    onUploadError,
  });
  const { uploadFile, uploading } = uploader ?? ownUploader;

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
        disabled={disabled || uploading}
        aria-busy={uploading}
        onClick={() => inputRef.current?.click()}
      >
        {uploading ? "アップロード中…" : label}
      </Button>
      <input
        ref={inputRef}
        type="file"
        hidden
        tabIndex={-1}
        aria-hidden="true"
        onChange={onUpload}
        multiple={false}
        disabled={disabled || uploading}
        {...props}
      />
    </>
  );
};
