import { useState } from "react";
import type { MyListItemShow } from "@/types/models";

interface MyListItemsTableProps {
  items: MyListItemShow[];
  listId: number;
  onUpdate: () => void;
}

/**
 * マイリストアイテム一覧テーブルコンポーネント
 */
export const MyListItemsTable = ({
  items,
  listId,
  onUpdate,
}: MyListItemsTableProps) => {
  const [editingItemId, setEditingItemId] = useState<number | null>(null);
  const [editingNote, setEditingNote] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  if (items.length === 0) {
    return (
      <div className="text-center py-12 text-gray-600">
        アイテムがありません。記事をマイリストに追加してください。
      </div>
    );
  }

  const handleEditNote = (item: MyListItemShow) => {
    setEditingItemId(item.id);
    setEditingNote(item.note || "");
  };

  const handleSaveNote = async (itemId: number) => {
    try {
      setIsLoading(true);
      setError(null);

      const response = await fetch(`/api/v1/mylist/${listId}/items/${itemId}`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        credentials: "include",
        body: JSON.stringify({
          note: editingNote.trim() || null,
        }),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.error || "更新に失敗しました");
      }

      setEditingItemId(null);
      onUpdate();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  const handleCancelEdit = () => {
    setEditingItemId(null);
    setEditingNote("");
  };

  const handleDelete = async (itemId: number) => {
    if (!confirm("このアイテムをリストから削除しますか?")) {
      return;
    }

    try {
      setIsLoading(true);
      setError(null);

      const response = await fetch(`/api/v1/mylist/${listId}/items/${itemId}`, {
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

      onUpdate();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  const handleMoveUp = async (item: MyListItemShow, index: number) => {
    if (index === 0) return;

    try {
      setIsLoading(true);
      setError(null);

      // 現在のアイテムと1つ上のアイテムの位置を入れ替え
      const currentItem = items[index];
      const prevItem = items[index - 1];

      const response = await fetch(`/api/v1/mylist/${listId}/items/reorder`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        credentials: "include",
        body: JSON.stringify({
          items: [
            { id: currentItem.id, position: prevItem.position },
            { id: prevItem.id, position: currentItem.position },
          ],
        }),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.error || "並び替えに失敗しました");
      }

      onUpdate();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  const handleMoveDown = async (item: MyListItemShow, index: number) => {
    if (index === items.length - 1) return;

    try {
      setIsLoading(true);
      setError(null);

      // 現在のアイテムと1つ下のアイテムの位置を入れ替え
      const currentItem = items[index];
      const nextItem = items[index + 1];

      const response = await fetch(`/api/v1/mylist/${listId}/items/reorder`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        credentials: "include",
        body: JSON.stringify({
          items: [
            { id: currentItem.id, position: nextItem.position },
            { id: nextItem.id, position: currentItem.position },
          ],
        }),
      });

      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.error || "並び替えに失敗しました");
      }

      onUpdate();
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div>
      {error && (
        <div className="alert alert-danger mb-4" role="alert">
          {error}
        </div>
      )}

      <div className="overflow-x-auto">
        <table className="table-auto w-full">
          <thead>
            <tr className="border-b">
              <th className="text-left p-3 w-16">順序</th>
              <th className="text-left p-3 w-24">サムネ</th>
              <th className="text-left p-3">タイトル</th>
              <th className="text-left p-3">投稿者</th>
              <th className="text-left p-3">メモ</th>
              <th className="text-left p-3">追加日</th>
              <th className="text-center p-3 w-32">操作</th>
            </tr>
          </thead>
          <tbody>
            {items.map((item, index) => {
              const isNonPublic = item.is_article_public === false;

              return (
                <tr
                  key={item.id}
                  className={`border-b hover:bg-gray-50 ${isNonPublic ? "bg-gray-100" : ""}`}
                >
                  <td className="p-3">
                    <div className="flex flex-col gap-1">
                      <button
                        type="button"
                        onClick={() => handleMoveUp(item, index)}
                        disabled={isLoading || index === 0}
                        className="btn btn-xs btn-secondary"
                        aria-label="上へ移動"
                      >
                        ↑
                      </button>
                      <button
                        type="button"
                        onClick={() => handleMoveDown(item, index)}
                        disabled={isLoading || index === items.length - 1}
                        className="btn btn-xs btn-secondary"
                        aria-label="下へ移動"
                      >
                        ↓
                      </button>
                    </div>
                  </td>
                  <td className="p-3">
                    {item.article.thumbnail ? (
                      <img
                        src={item.article.thumbnail}
                        alt=""
                        className="w-16 h-16 object-cover rounded"
                      />
                    ) : (
                      <div className="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">
                        No Image
                      </div>
                    )}
                  </td>
                  <td className="p-3">
                    <div>
                      {isNonPublic ? (
                        <>
                          <div className="text-gray-700">
                            {item.article.title}
                          </div>
                          <span className="badge badge-warning mt-1">
                            非公開
                          </span>
                        </>
                      ) : (
                        <a
                          href={`/articles/${item.article.slug}`}
                          className="text-blue-600 hover:underline"
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          {item.article.title}
                        </a>
                      )}
                    </div>
                  </td>
                  <td className="p-3">
                    <div className="flex items-center gap-2">
                      {item.article.user.profile?.avatar ? (
                        <img
                          src={item.article.user.profile.avatar}
                          alt=""
                          className="w-8 h-8 rounded-full"
                        />
                      ) : (
                        <div className="w-8 h-8 bg-gray-300 rounded-full"></div>
                      )}
                      <span className="text-sm">
                        {item.article.user.profile?.nickname ||
                          item.article.user.name}
                      </span>
                    </div>
                  </td>
                  <td className="p-3">
                    {editingItemId === item.id ? (
                      <div className="flex gap-2">
                        <input
                          type="text"
                          value={editingNote}
                          onChange={(e) => setEditingNote(e.target.value)}
                          className="form-input form-input-sm flex-1"
                          maxLength={255}
                          disabled={isLoading}
                        />
                        <button
                          type="button"
                          onClick={() => handleSaveNote(item.id)}
                          disabled={isLoading}
                          className="btn btn-xs btn-primary"
                        >
                          保存
                        </button>
                        <button
                          type="button"
                          onClick={handleCancelEdit}
                          disabled={isLoading}
                          className="btn btn-xs btn-secondary"
                        >
                          キャンセル
                        </button>
                      </div>
                    ) : (
                      <div
                        className="text-sm cursor-pointer hover:bg-gray-100 p-1 rounded"
                        onClick={() => handleEditNote(item)}
                      >
                        {item.note || (
                          <span className="text-gray-400">メモを追加</span>
                        )}
                      </div>
                    )}
                  </td>
                  <td className="p-3">
                    {new Date(item.created_at).toLocaleDateString("ja-JP")}
                  </td>
                  <td className="p-3">
                    <div className="flex justify-center">
                      <button
                        type="button"
                        onClick={() => handleDelete(item.id)}
                        disabled={isLoading}
                        className="btn btn-sm btn-danger"
                        aria-label="削除"
                      >
                        削除
                      </button>
                    </div>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
};
