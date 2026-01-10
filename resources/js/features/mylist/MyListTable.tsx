import { useState } from "react";
import type { MyListShow } from "@/types/models";

interface MyListTableProps {
  lists: MyListShow[];
  onEdit: (list: MyListShow) => void;
  onDelete: (list: MyListShow) => void;
}

/**
 * マイリスト一覧テーブルコンポーネント
 */
export const MyListTable = ({ lists, onEdit, onDelete }: MyListTableProps) => {
  if (lists.length === 0) {
    return (
      <div className="text-center py-12 text-gray-600">
        マイリストがありません。新しく作成してください。
      </div>
    );
  }

  return (
    <div className="overflow-x-auto">
      <table className="table-auto w-full">
        <thead>
          <tr className="border-b">
            <th className="text-left p-3">タイトル</th>
            <th className="text-left p-3">公開状態</th>
            <th className="text-left p-3">アイテム数</th>
            <th className="text-left p-3">更新日</th>
            <th className="text-center p-3 w-32">操作</th>
          </tr>
        </thead>
        <tbody>
          {lists.map((list) => (
            <tr key={list.id} className="border-b hover:bg-gray-50">
              <td className="p-3">
                <div className="font-medium">{list.title}</div>
                {list.note && (
                  <div className="text-sm text-gray-600 mt-1 line-clamp-2">
                    {list.note}
                  </div>
                )}
              </td>
              <td className="p-3">
                {list.is_public ? (
                  <span className="badge badge-success">公開</span>
                ) : (
                  <span className="badge badge-secondary">非公開</span>
                )}
              </td>
              <td className="p-3">{list.items_count || 0}件</td>
              <td className="p-3">
                {new Date(list.updated_at).toLocaleDateString("ja-JP")}
              </td>
              <td className="p-3">
                <div className="flex gap-2 justify-center">
                  <button
                    type="button"
                    onClick={() => onEdit(list)}
                    className="btn btn-sm btn-secondary"
                    aria-label={`${list.title}を編集`}
                  >
                    編集
                  </button>
                  <button
                    type="button"
                    onClick={() => onDelete(list)}
                    className="btn btn-sm btn-danger"
                    aria-label={`${list.title}を削除`}
                  >
                    削除
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

interface MyListEditModalProps {
  list: MyListShow | null;
  onClose: () => void;
  onSuccess: () => void;
}

/**
 * マイリスト編集モーダル
 */
export const MyListEditModal = ({
  list,
  onClose,
  onSuccess,
}: MyListEditModalProps) => {
  const [title, setTitle] = useState(list?.title || "");
  const [note, setNote] = useState(list?.note || "");
  const [isPublic, setIsPublic] = useState(list?.is_public || false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!title.trim()) {
      setError("タイトルを入力してください");
      return;
    }

    try {
      setIsLoading(true);
      setError(null);

      const method = list ? "PATCH" : "POST";
      const url = list ? `/api/v1/mylist/${list.id}` : "/api/v1/mylist";

      const response = await fetch(url, {
        method,
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        credentials: "include",
        body: JSON.stringify({
          title: title.trim(),
          note: note.trim() || null,
          is_public: isPublic,
        }),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.error || "保存に失敗しました");
      }

      onSuccess();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  if (!list && list !== null) {
    return null;
  }

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div
        className="modal-content modal-md"
        onClick={(e) => e.stopPropagation()}
      >
        <form onSubmit={handleSubmit}>
          <div className="modal-header">
            <h3 className="modal-title">
              {list ? "マイリストを編集" : "マイリストを作成"}
            </h3>
            <button
              type="button"
              onClick={onClose}
              className="btn-close"
              aria-label="閉じる"
            >
              ×
            </button>
          </div>

          <div className="modal-body">
            {error && (
              <div className="alert alert-danger mb-4" role="alert">
                {error}
              </div>
            )}

            <div className="mb-4">
              <label htmlFor="title" className="form-label required">
                タイトル
              </label>
              <input
                id="title"
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                className="form-input"
                maxLength={120}
                required
                disabled={isLoading}
              />
            </div>

            <div className="mb-4">
              <label htmlFor="note" className="form-label">
                メモ
              </label>
              <textarea
                id="note"
                value={note}
                onChange={(e) => setNote(e.target.value)}
                className="form-textarea"
                rows={4}
                disabled={isLoading}
              />
            </div>

            <div className="mb-4">
              <label className="flex items-center gap-2">
                <input
                  type="checkbox"
                  checked={isPublic}
                  onChange={(e) => setIsPublic(e.target.checked)}
                  className="form-checkbox"
                  disabled={isLoading}
                />
                <span>このリストを公開する</span>
              </label>
              <div className="text-sm text-gray-600 mt-1">
                公開すると、URLを知っている人が閲覧できます
              </div>
            </div>
          </div>

          <div className="modal-footer">
            <button
              type="button"
              onClick={onClose}
              className="btn btn-secondary"
              disabled={isLoading}
            >
              キャンセル
            </button>
            <button
              type="submit"
              className="btn btn-primary"
              disabled={isLoading || !title.trim()}
            >
              {isLoading ? "保存中..." : "保存"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

interface MyListDeleteModalProps {
  list: MyListShow | null;
  onClose: () => void;
  onSuccess: () => void;
}

/**
 * マイリスト削除確認モーダル
 */
export const MyListDeleteModal = ({
  list,
  onClose,
  onSuccess,
}: MyListDeleteModalProps) => {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleDelete = async () => {
    if (!list) return;

    try {
      setIsLoading(true);
      setError(null);

      const response = await fetch(`/api/v1/mylist/${list.id}`, {
        method: "DELETE",
        headers: {
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        credentials: "include",
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.error || "削除に失敗しました");
      }

      onSuccess();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  if (!list) {
    return null;
  }

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div
        className="modal-content modal-sm"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="modal-header">
          <h3 className="modal-title">マイリストを削除</h3>
          <button
            type="button"
            onClick={onClose}
            className="btn-close"
            aria-label="閉じる"
          >
            ×
          </button>
        </div>

        <div className="modal-body">
          {error && (
            <div className="alert alert-danger mb-4" role="alert">
              {error}
            </div>
          )}

          <p className="mb-4">
            「<strong>{list.title}</strong>」を削除してもよろしいですか？
          </p>
          <p className="text-sm text-gray-600">
            この操作は取り消せません。リスト内のアイテムもすべて削除されます。
          </p>
        </div>

        <div className="modal-footer">
          <button
            type="button"
            onClick={onClose}
            className="btn btn-secondary"
            disabled={isLoading}
          >
            キャンセル
          </button>
          <button
            type="button"
            onClick={handleDelete}
            className="btn btn-danger"
            disabled={isLoading}
          >
            {isLoading ? "削除中..." : "削除"}
          </button>
        </div>
      </div>
    </div>
  );
};
