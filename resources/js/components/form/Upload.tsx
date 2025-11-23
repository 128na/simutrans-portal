import axios, { AxiosError } from "axios";
import InputFile from "../ui/InputFile";

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
      console.error("アップロード失敗", error);
      if (error instanceof AxiosError) {
        alert(
          `アップロードに失敗しました：${error.response?.data.message ?? "不明なエラー"}`
        );
      }
    }
  };

  return <InputFile onChange={onUpload} multiple={false} {...props} />;
};
