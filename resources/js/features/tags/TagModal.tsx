import { useState } from "react";
import axios from "axios";
import Input from "@/components/ui/Input";
import Textarea from "@/components/ui/Textarea";
import Button from "@/components/ui/Button";
import { useAxiosErrorState } from "@/hooks/errorState";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { Modal } from "@/components/ui/Modal";
import { isValidationError } from "@/lib/errorHandler";
import { useErrorHandler } from "@/hooks/useErrorHandler";
import { FormCaption } from "@/components/ui/FormCaption";

type Props = {
  tag: Tag.MypageEdit | Tag.New | null;
  onClose?: () => void;
  onSave?: (tag: Tag.MypageEdit) => void;
};

export const TagModal = ({ tag, onClose, onSave }: Props) => {
  const [name, setName] = useState(tag?.name ?? "");
  const [description, setDescription] = useState(tag?.description ?? "");
  const { setError, getError } = useAxiosErrorState();
  const { handleErrorWithContext } = useErrorHandler({ component: "TagModal" });

  // tag が null の場合はモーダルを非表示にする
  if (!tag) return null;

  const handleSave = async () => {
    try {
      const res = tag.id
        ? await axios.post(`/api/v2/tags/${tag.id}`, { description })
        : await axios.post(`/api/v2/tags`, { name, description });
      if ((res.status === 200 || res.status === 201) && onSave) {
        onSave(res.data.data as Tag.MypageEdit);
      }
    } catch (error) {
      if (isValidationError(error)) {
        setError(error);
      } else {
        handleErrorWithContext(error, { action: "save" });
      }
    }
  };

  return (
    <Modal
      title={tag.id ? "タグ編集" : "タグ新規作成"}
      onClose={onClose}
      modalClass="max-w-2xl"
    >
      <div className="grid gap-x-4 mb-4">
        <div>
          <FormCaption>名前</FormCaption>
          <TextError>{getError("name")?.join("\n")}</TextError>
          <Input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            disabled={!!tag.id}
            className={tag.id ? "bg-c-sub" : ""}
          />
          {tag.id ? (
            <TextSub className="mb-2">タグ名は編集できません。</TextSub>
          ) : null}
        </div>
        <div>
          <FormCaption>説明</FormCaption>
          <TextError>{getError("description")?.join("\n")}</TextError>
          <Textarea
            rows={4}
            value={description}
            onChange={(e) => setDescription(e.target.value)}
          />
        </div>
      </div>

      <div className="flex justify-end space-x-2">
        <Button onClick={handleSave}>保存</Button>
      </div>
    </Modal>
  );
};
