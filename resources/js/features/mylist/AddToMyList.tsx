import { useState } from "react";
import type { MyListShow, MyListCreateRequest } from "@/types/models";

interface AddToMyListButtonProps {
  articleId: number;
  className?: string;
  onSuccess?: () => void;
}

/**
 * 記事をマイリストに追加するボタンコンポーネント
 * 記事カード・詳細ページに配置される
 */
export const AddToMyListButton = ({
  articleId,
  className = "",
  onSuccess,
}: AddToMyListButtonProps) => {
  const [isOpen, setIsOpen] = useState(false);

  const handleClick = () => {
    setIsOpen(true);
  };

  const handleClose = () => {
    setIsOpen(false);
  };

  const handleSuccess = () => {
    onSuccess?.();
    setIsOpen(false);
  };

  return (
    <>
      <button
        type="button"
        onClick={handleClick}
        className={`btn btn-secondary ${className}`}
        aria-label="マイリストに追加"
      >
        <span className="icon-plus"></span>
        マイリスト
      </button>

      {isOpen && (
        <AddToMyListModal
          articleId={articleId}
          onClose={handleClose}
          onSuccess={handleSuccess}
        />
      )}
    </>
  );
};

interface AddToMyListModalProps {
  articleId: number;
  onClose: () => void;
  onSuccess: () => void;
}

/**
 * マイリスト選択・追加モーダル
 */
const AddToMyListModal = ({
  articleId,
  onClose,
  onSuccess,
}: AddToMyListModalProps) => {
  const [lists, setLists] = useState<MyListShow[]>([]);
  const [selectedListIds, setSelectedListIds] = useState<Set<number>>(
    new Set()
  );
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isCreating, setIsCreating] = useState(false);
  const [newListTitle, setNewListTitle] = useState("");

  // リスト一覧を取得
  useState(() => {
    const fetchLists = async () => {
      try {
        setIsLoading(true);
        const response = await fetch("/api/v1/mylist", {
          credentials: "include",
        });

        if (!response.ok) {
          throw new Error("リストの取得に失敗しました");
        }

        const data = await response.json();
        if (data.ok && data.data?.lists) {
          setLists(data.data.lists);
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : "エラーが発生しました");
      } finally {
        setIsLoading(false);
      }
    };

    fetchLists();
  });

  const handleToggleList = (listId: number) => {
    const newSelected = new Set(selectedListIds);
    if (newSelected.has(listId)) {
      newSelected.delete(listId);
    } else {
      newSelected.add(listId);
    }
    setSelectedListIds(newSelected);
  };

  const handleCreateList = async () => {
    if (!newListTitle.trim()) {
      setError("タイトルを入力してください");
      return;
    }

    try {
      setIsCreating(true);
      setError(null);

      const requestBody: MyListCreateRequest = {
        title: newListTitle.trim(),
      };

      const response = await fetch("/api/v1/mylist", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        credentials: "include",
        body: JSON.stringify(requestBody),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.error || "リストの作成に失敗しました");
      }

      const data = await response.json();
      if (data.ok && data.data?.list) {
        setLists([...lists, data.data.list]);
        setNewListTitle("");
        setError(null);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsCreating(false);
    }
  };

  const handleAddToLists = async () => {
    if (selectedListIds.size === 0) {
      setError("リストを選択してください");
      return;
    }

    try {
      setIsLoading(true);
      setError(null);

      const promises = Array.from(selectedListIds).map(async (listId) => {
        const response = await fetch(`/api/v1/mylist/${listId}/items`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN":
              document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "",
          },
          credentials: "include",
          body: JSON.stringify({ article_id: articleId }),
        });

        if (!response.ok && response.status !== 409) {
          // 409は重複エラーなので無視
          const data = await response.json();
          throw new Error(data.error || "追加に失敗しました");
        }

        return response;
      });

      await Promise.all(promises);
      onSuccess();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div
        className="modal-content modal-md"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="modal-header">
          <h3 className="modal-title">マイリストに追加</h3>
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

          {isLoading ? (
            <div className="text-center py-8">読み込み中...</div>
          ) : lists.length === 0 ? (
            <div className="text-center py-8 text-gray-600">
              マイリストがありません。新しく作成してください。
            </div>
          ) : (
            <div className="space-y-2 mb-6">
              {lists.map((list) => (
                <label
                  key={list.id}
                  className="flex items-center gap-3 p-3 border rounded hover:bg-gray-50 cursor-pointer"
                >
                  <input
                    type="checkbox"
                    checked={selectedListIds.has(list.id)}
                    onChange={() => handleToggleList(list.id)}
                    className="form-checkbox"
                  />
                  <div className="flex-1">
                    <div className="font-medium">{list.title}</div>
                    {list.note && (
                      <div className="text-sm text-gray-600 mt-1">
                        {list.note}
                      </div>
                    )}
                  </div>
                  {list.is_public && (
                    <span className="badge badge-secondary">公開</span>
                  )}
                </label>
              ))}
            </div>
          )}

          <div className="border-t pt-4">
            <h4 className="font-medium mb-3">新しいリストを作成</h4>
            <div className="flex gap-2">
              <input
                type="text"
                value={newListTitle}
                onChange={(e) => setNewListTitle(e.target.value)}
                placeholder="リストのタイトル"
                className="form-input flex-1"
                maxLength={120}
                disabled={isCreating}
              />
              <button
                type="button"
                onClick={handleCreateList}
                disabled={isCreating || !newListTitle.trim()}
                className="btn btn-secondary"
              >
                {isCreating ? "作成中..." : "作成"}
              </button>
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
            type="button"
            onClick={handleAddToLists}
            disabled={isLoading || selectedListIds.size === 0}
            className="btn btn-primary"
          >
            {isLoading ? "追加中..." : "追加"}
          </button>
        </div>
      </div>
    </div>
  );
};
