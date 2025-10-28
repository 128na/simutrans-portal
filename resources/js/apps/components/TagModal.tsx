import { useEffect, useState } from "react";
import { CloseIcon } from "../svg/CloseIcon";
import axios from "axios";

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
            <button
              onClick={onClose}
              type="button"
              className="text-gray-400 hover:text-gray-900 rounded-lg text-sm p-1.5"
            >
              <CloseIcon />
            </button>
          </div>

          <div className="grid gap-x-4 mb-4">
            <label className="block text-sm/6 font-semibold text-gray-900">
              タグ名
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                disabled={!!tag.id}
                className="block w-full border border-gray-300 rounded-lg px-4 py-2"
              />
            </label>

            <label className="block text-sm/6 font-semibold text-gray-900">
              説明
              <textarea
                rows={4}
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                className="block w-full border border-gray-300 rounded-lg px-4 py-2"
              />
            </label>
          </div>

          <div className="flex justify-end space-x-2">
            <button
              onClick={onClose}
              className="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100"
            >
              キャンセル
            </button>
            <button
              onClick={handleSave}
              className="px-4 py-2  bg-brand text-white rounded-lg hover:bg-brand/90"
            >
              保存
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
