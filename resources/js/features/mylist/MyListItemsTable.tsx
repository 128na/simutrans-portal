import axios from "axios";
import { useState } from "react";
import Button from "@/components/ui/Button";
import Input from "@/components/ui/Input";
import TextBadge from "@/components/ui/TextBadge";
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
    try {
      setIsLoading(true);
      setError(null);

      await axios.patch(`/api/v1/mylist/${listId}/items/${itemId}`, {
        note: editingNote.trim() || null,
      });

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

      await axios.delete(`/api/v1/mylist/${listId}/items/${itemId}`);

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

      await axios.patch(`/api/v1/mylist/${listId}/items/reorder`, {
        items: [
          { id: currentItem.id, position: prevItem.position },
          { id: prevItem.id, position: currentItem.position },
        ],
      });

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

      await axios.patch(`/api/v1/mylist/${listId}/items/reorder`, {
        items: [
          { id: currentItem.id, position: nextItem.position },
          { id: nextItem.id, position: currentItem.position },
        ],
      });

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
              const isNonPublic = item.is_article_public === false;

              return (
                <tr key={item.id} className={isNonPublic ? "bg-gray-100" : ""}>
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
                  </td>
                  <td>
                    <div>
                      {isNonPublic ? (
                        <>
                          <div className="text-gray-700">
                            {item.article.title}
                          </div>
                          <TextBadge variant="warn">非公開</TextBadge>
                        </>
                      ) : (
                        <a
                          href={`/articles/${item.article.slug}`}
                          className="v2-link"
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          {item.article.title}
                        </a>
                      )}
                    </div>
                  </td>
                  <td>
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
                  <td>
                    {editingItemId === item.id ? (
                      <div className="flex gap-2">
                        <Input
                          value={editingNote}
                          onChange={(e) => setEditingNote(e.target.value)}
                          maxLength={255}
                          disabled={isLoading}
                          className="flex-1"
                        />
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
                    ) : (
                      <div
                        className="text-sm cursor-pointer v2-hover-bg-sub p-1 rounded"
                        onClick={() => handleEditNote(item)}
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
                    <div className="flex justify-center">
                      <Button
                        onClick={() => handleDelete(item.id)}
                        disabled={isLoading}
                        variant="danger"
                        size="sm"
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
