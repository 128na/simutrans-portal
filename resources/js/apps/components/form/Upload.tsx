import axios from "axios";
import InputFile from "../ui/InputFile";

type Prop = {
  onUploaded?: (attachment: Attachment) => void;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const Upload = ({ onUploaded, ...props }: Prop) => {
  const onUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    // FormData にファイルを詰める
    const formData = new FormData();
    formData.append("file", file); // ← バックエンド側のパラメータ名に合わせる

    try {
      const response = await axios.post("/api/v2/attachments", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      console.log("アップロード完了", response.data.data);
      onUploaded?.(response.data.data);
    } catch (error) {
      console.error("アップロード失敗", error);
    }
  };

  return (
    <InputFile
      onChange={onUpload}
      multiple={false}
      accept="image/*"
      {...props}
    />
  );
};
