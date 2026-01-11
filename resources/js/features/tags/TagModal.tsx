import { useState } from "react";
import axios from "axios";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { Modal } from "@/components/ui/Modal";
import { useErrorHandler } from "@/hooks/useErrorHandler";
import { useModelModal } from "@/hooks/useModelModal";
import { FormCaption } from "@/components/ui/FormCaption";
import Input from "@/components/ui/Input";
import Textarea from "@/components/ui/Textarea";
import Button from "@/components/ui/Button";

type Props = {
  tag: Tag.MypageEdit | Tag.New | null;
  onClose?: () => void;
  onSave?: (tag: Tag.MypageEdit) => void;
};

export const TagModal = ({ tag, onClose, onSave }: Props) => {
  const [name, setName] = useState(tag?.name ?? "");
  const [description, setDescription] = useState(tag?.description ?? "");
  const { error, isLoading, getError, handleSave } = useModelModal();
  const { handleErrorWithContext } = useErrorHandler({ component: "TagModal" });

  // tag が null の場合はモーダルを非表示にする
  if (!tag) return null;

  const onSaveClick = async () => {
    await handleSave(
      () =>
        tag.id
          ? axios.post(`/api/v2/tags/${tag.id}`, { description })
          : axios.post(`/api/v2/tags`, { name, description }),
      {
        onSuccess: (res) => {
          onSave?.(res.data as Tag.MypageEdit);
        },
        onError: (err) => {
          handleErrorWithContext(err, { action: "save" });
        },
      }
    );
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
          <TextError>
            {(() => {
              const nameError = getError("name");
              if (Array.isArray(nameError)) {
                return nameError.join("\n");
              }
              return nameError || undefined;
            })()}
          </TextError>
          <Input
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
          <TextError>
            {(() => {
              const descError = getError("description");
              if (Array.isArray(descError)) {
                return descError.join("\n");
              }
              return descError || undefined;
            })()}
          </TextError>
          <Textarea
            rows={4}
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            className="block w-full"
            maxLength={1024}
          />
        </div>
      </div>

      <div className="flex justify-end space-x-2">
        <Button onClick={onSaveClick} size="lg" disabled={isLoading}>
          {isLoading ? "保存中..." : "保存"}
        </Button>
      </div>
    </Modal>
  );
};
