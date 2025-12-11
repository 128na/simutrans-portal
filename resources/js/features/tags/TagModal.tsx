import { useState } from "react";
import axios from "axios";
import { useAxiosErrorState } from "@/hooks/errorState";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { Modal } from "@/components/ui/Modal";
import { isValidationError } from "@/lib/errorHandler";
import { useErrorHandler } from "@/hooks/useErrorHandler";
import { FormCaption } from "@/components/ui/FormCaption";
import V2Input from "@/components/ui/v2/V2Input";
import V2Textarea from "@/components/ui/v2/V2Textarea";
import V2Button from "@/components/ui/v2/V2Button";

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
          <V2Input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            disabled={!!tag.id}
            className="block w-full"
            required
            maxLength={20}
          />
          {tag.id ? (
            <TextSub className="mb-2">タグ名は編集できません。</TextSub>
          ) : null}
        </div>
        <div>
          <FormCaption>説明</FormCaption>
          <TextError>{getError("description")?.join("\n")}</TextError>
          <V2Textarea
            rows={4}
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            className="block w-full"
            maxLength={1024}
          />
        </div>
      </div>

      <div className="flex justify-end space-x-2">
        <V2Button onClick={handleSave} size="lg">
          保存
        </V2Button>
      </div>
    </Modal>
  );
};
