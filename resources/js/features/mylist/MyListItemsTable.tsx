import axios from "axios";
import { useState } from "react";
import Button from "@/components/ui/Button";
import Input from "@/components/ui/Input";
import { useApiCall } from "@/hooks/useApiCall";
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
  const { call, isLoading } = useApiCall();
  const [editingItemId, setEditingItemId] = useState<number | null>(null);
  const [editingNote, setEditingNote] = useState("");
  const [error, setError] = useState<string | null>(null);

  if (items.length === 0) {
    return (
      <div className="text-center py-12 v2-text-sub">
        アイテムがありません。記事をマイリストに追加してください。
      </div>
    );
  }

  const handleEditNote = (item: MyListItemShow) => {
    setEditingItemId(item.id);
    setEditingNote(item.note || "");
  };

  const handleSaveNote = async (itemId: number) => {
    setError(null);

    const result = await call(
      () =>
        axios.patch(`/api/v1/mylist/${listId}/items/${itemId}`, {
          note: editingNote.trim() || null,
        }),
      {
        successMessage: "メモを保存しました",
        onSuccess: () => {
          setEditingItemId(null);
          onUpdate();
        },
      }
    );

    // バリデーションエラーがある場合は表示
    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
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

    setError(null);

    const result = await call(
      () => axios.delete(`/api/v1/mylist/${listId}/items/${itemId}`),
      {
        successMessage: "アイテムを削除しました",
        onSuccess: () => onUpdate(),
      }
    );

    // バリデーションエラーがある場合は表示
    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
    }
  };

  const handleMoveUp = async (item: MyListItemShow, index: number) => {
    if (index === 0) return;

    setError(null);

    // 現在のアイテムと1つ上のアイテムの位置を入れ替え
    const currentItem = items[index];
    const prevItem = items[index - 1];

    const result = await call(
      () =>
        axios.patch(`/api/v1/mylist/${listId}/items/reorder`, {
          items: [
            { id: currentItem.id, position: prevItem.position },
            { id: prevItem.id, position: currentItem.position },
          ],
        }),
      {
        successMessage: "並び替えを保存しました",
        onSuccess: () => onUpdate(),
      }
    );

    // バリデーションエラーがある場合は表示
    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
    }
  };

  const handleMoveDown = async (item: MyListItemShow, index: number) => {
    if (index === items.length - 1) return;

    setError(null);

    // 現在のアイテムと1つ下のアイテムの位置を入れ替え
    const currentItem = items[index];
    const nextItem = items[index + 1];

    const result = await call(
      () =>
        axios.patch(`/api/v1/mylist/${listId}/items/reorder`, {
          items: [
            { id: currentItem.id, position: nextItem.position },
            { id: nextItem.id, position: currentItem.position },
          ],
        }),
      {
        successMessage: "並び替えを保存しました",
        onSuccess: () => onUpdate(),
      }
    );

    // バリデーションエラーがある場合は表示
    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
    }
  };

  return (
    <div>
      {error && (
        <div className="v2-card v2-card-danger mb-4" role="alert">
          {error}
        </div>
      )}

      <div className="v2-table-wrapper">
        <table className="v2-table v2-table-fixed">
          <thead>
            <tr>
              <th className="w-16">順序</th>
              <th className="w-24">サムネ</th>
              <th>タイトル</th>
              <th>投稿者</th>
              <th>メモ</th>
              <th>追加日</th>
              <th className="w-32">操作</th>
            </tr>
          </thead>
          <tbody>
            {items.map((item, index) => {
              const isPublic =
                "url" in item.article && "published_at" in item.article;

              return (
                <tr key={item.id} className={!isPublic ? "bg-gray-100" : ""}>
                  <td>
                    <div className="flex flex-col gap-1">
                      <Button
                        onClick={() => handleMoveUp(item, index)}
                        disabled={isLoading || index === 0}
                        variant="sub"
                        size="sm"
                        aria-label="上へ移動"
                      >
                        ↑
                      </Button>
                      <Button
                        onClick={() => handleMoveDown(item, index)}
                        disabled={isLoading || index === items.length - 1}
                        variant="sub"
                        size="sm"
                        aria-label="下へ移動"
                      >
                        ↓
                      </Button>
                    </div>
                  </td>
                  <td>
                    {isPublic ? (
                      <>
                        {item.article.thumbnail ? (
                          <img
                            src={item.article.thumbnail}
                            alt=""
                            className="w-16 h-16 object-cover rounded"
                          />
                        ) : (
                          <div className="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-xs v2-text-sub">
                            No Image
                          </div>
                        )}
                      </>
                    ) : (
                      <div className="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-xs v2-text-sub">
                        No Image
                      </div>
                    )}
                  </td>
                  <td>
                    <div>
                      {isPublic ? (
                        <a
                          href={item.article.url || ""}
                          className="v2-link"
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          {item.article.title}
                        </a>
                      ) : (
                        <>
                          <div className="text-gray-700">
                            {item.article.title}
                          </div>
                        </>
                      )}
                    </div>
                  </td>
                  <td>
                    {isPublic ? (
                      <div className="flex items-center gap-2">
                        <img
                          src={item.article.user!.avatar}
                          alt=""
                          className="w-8 h-8 rounded-full"
                        />
                        <span className="text-sm">
                          {item.article.user!.name}
                        </span>
                      </div>
                    ) : (
                      <span className="text-sm v2-text-sub">-</span>
                    )}
                  </td>
                  <td>
                    {editingItemId === item.id ? (
                      <>
                        <Input
                          value={editingNote}
                          onChange={(e) => setEditingNote(e.target.value)}
                          disabled={isLoading}
                          className="w-full mb-2"
                        />
                        <div className="space-x-2">
                          <Button
                            onClick={() => handleSaveNote(item.id)}
                            disabled={isLoading}
                            variant="primary"
                            size="sm"
                          >
                            保存
                          </Button>
                          <Button
                            onClick={handleCancelEdit}
                            disabled={isLoading}
                            variant="sub"
                            size="sm"
                          >
                            キャンセル
                          </Button>
                        </div>
                      </>
                    ) : (
                      <div
                        className="text-sm cursor-pointer v2-hover-bg-sub p-1 rounded truncate"
                        onClick={() => handleEditNote(item)}
                        title={item.note || "メモを追加"}
                      >
                        {item.note || (
                          <span className="v2-text-sub">メモを追加</span>
                        )}
                      </div>
                    )}
                  </td>
                  <td>
                    {new Date(item.created_at).toLocaleDateString("ja-JP")}
                  </td>
                  <td>
                    <div className="flex justify-center flex-wrap gap-2">
                      {item.article.download_url && (
                        <a
                          className="v2-button v2-button-md v2-button-primary inline-block"
                          href={item.article.download_url}
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          ダウンロード
                        </a>
                      )}
                      {item.article.addon_page_url && (
                        <a
                          href={item.article.addon_page_url}
                          className="v2-button v2-button-md v2-button-primary inline-block"
                        >
                          掲載ページ
                        </a>
                      )}
                      <Button
                        onClick={() => handleDelete(item.id)}
                        disabled={isLoading}
                        variant="danger"
                        aria-label="削除"
                      >
                        削除
                      </Button>
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
