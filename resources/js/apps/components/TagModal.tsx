import { useEffect, useState } from "react";
import axios from "axios";
import Input from "../elements/Input";
import Textarea from "../elements/Textarea";
import Button from "../elements/Button";
import ButtonOutline from "../elements/ButtonOuline";
import ButtonClose from "../elements/ButtonClose";

type Props = {
  tag: Tag | NewTag | null;
  onClose?: () => void;
  onSave?: (tag: Tag) => void;
};

export const TagModal = ({ tag, onClose, onSave }: Props) => {
  const [name, setName] = useState("");
  const [description, setDescription] = useState("");

  // tag が変わったときにフォームを更新
  useEffect(() => {
    if (tag) {
      setName(tag.name ?? "");
      setDescription(tag.description ?? "");
    } else {
      setName("");
      setDescription("");
    }
  }, [tag]);

  // tag が null の場合はモーダルを非表示にする
  if (!tag) return null;

  const handleSave = async () => {
    try {
      const res = tag.id
        ? await axios.post(`/api/v2/tags/${tag.id}`, { description })
        : await axios.post(`/api/v2/tags`, { name, description });
      console.log({ res });
      if ((res.status === 200 || res.status === 201) && onSave) {
        onSave(res.data as Tag);
      }
    } catch (error) {
      // TODO: エラー表示
      console.log("error", error);
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
              className={!!tag.id ? "bg-gray-100" : ""}
            >
              タグ名
            </Input>

            <Textarea
              rows={4}
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            >
              説明
            </Textarea>
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
