import axios from "axios";
import { handleError } from "@/lib/errorHandler";
import Input from "../ui/Input";

type Prop = {
  onUploaded?: (attachment: Attachment.MypageEdit) => void;
  onUploadingChange?: (uploading: boolean) => void;
  onUploadError?: (error: unknown) => void;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const Upload = ({
  onUploaded,
  onUploadingChange,
  onUploadError,
  ...props
}: Prop) => {
  const onUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

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

  return <Input type="file" onChange={onUpload} multiple={false} {...props} />;
};
