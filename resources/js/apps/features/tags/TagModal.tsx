import { useState } from "react";
import axios, { AxiosError } from "axios";
import Input from "@/apps/components/ui/Input";
import Textarea from "@/apps/components/ui/Textarea";
import Button from "@/apps/components/ui/Button";
import ButtonOutline from "@/apps/components/ui/ButtonOuline";
import ButtonClose from "@/apps/components/ui/ButtonClose";
import { useAxiosErrorState } from "../../state/errorState";
import TextError from "@/apps/components/ui/TextError";
import TextSub from "@/apps/components/ui/TextSub";

type Props = {
  tag: Tag.Listing | Tag.Creating | null;
  onClose?: () => void;
  onSave?: (tag: Tag.Listing) => void;
};

export const TagModal = ({ tag, onClose, onSave }: Props) => {
  const [name, setName] = useState(tag?.name ?? "");
  const [description, setDescription] = useState(tag?.description ?? "");
  const { setError, getError } = useAxiosErrorState();

  // tag が null の場合はモーダルを非表示にする
  if (!tag) return null;

  const handleSave = async () => {
    try {
      const res = tag.id
        ? await axios.post(`/api/v2/tags/${tag.id}`, { description })
        : await axios.post(`/api/v2/tags`, { name, description });
      if ((res.status === 200 || res.status === 201) && onSave) {
        onSave(res.data.data as Tag.Listing);
      }
    } catch (error) {
      console.log("error", error);
      if (error instanceof AxiosError) {
        setError(error);
      }
    }
  };

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
      aria-modal="true"
      role="dialog"
    >
      <div className="relative p-4 w-full max-w-2xl">
        <div className="bg-white rounded-lg shadow p-5">
          <div className="flex justify-between items-center pb-3 mb-4">
            <h3 className="text-lg font-semibold text-gray-900 ">
              {tag.id ? "タグ編集" : "タグ新規作成"}
            </h3>
            <ButtonClose onClick={onClose} />
          </div>

          <div className="grid gap-x-4 mb-4">
            <Input
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              disabled={!!tag.id}
              className={tag.id ? "bg-gray-100" : ""}
            >
              名前
            </Input>
            {tag.id ? (
              <TextSub className="mb-2">タグ名は編集できません。</TextSub>
            ) : null}
            <TextError className="mb-2">
              {getError("name")?.join("\n")}
            </TextError>
            <Textarea
              rows={4}
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            >
              説明
            </Textarea>
            <TextError className="mb-2">
              {getError("description")?.join("\n")}
            </TextError>
          </div>

          <div className="flex justify-end space-x-2">
            <ButtonOutline onClick={onClose}>キャンセル</ButtonOutline>
            <Button onClick={handleSave}>保存</Button>
          </div>
        </div>
      </div>
    </div>
  );
};
