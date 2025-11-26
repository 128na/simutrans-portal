import axios from "axios";
import InputFile from "../ui/InputFile";
import { handleError } from "@/lib/errorHandler";

type Prop = {
  onUploaded?: (attachment: Attachment.MypageEdit) => void;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const Upload = ({ onUploaded, ...props }: Prop) => {
  const onUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("file", file);

    try {
      const response = await axios.post("/api/v2/attachments", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      onUploaded?.(response.data.data);
    } catch (error) {
      handleError(error, { component: "Upload", action: "upload" });
    }
  };

  return <InputFile onChange={onUpload} multiple={false} {...props} />;
};
