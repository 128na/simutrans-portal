import axios from "axios";
import { createRoot } from "react-dom/client";
import { useState, useEffect } from "react";
import { AppWrapper } from "../../components/AppWrapper";
import { extractErrorMessage } from "@/lib/errorHandler";
import { MyListItemsTable } from "../../features/mylist/MyListItemsTable";
import type { MyListItemShow } from "@/types/models";

const app = document.getElementById("app-mylist-detail");

if (app) {
  const listId = Number(app.getAttribute("data-mylist-id"));
  if (!listId) {
    throw new Error("MyList ID is not provided");
  }

  const App = () => {
    // 成功メッセージは表の操作（メモ保存、アイテム削除、並び替え）から showSuccess() で出力される
    const [items, setItems] = useState<MyListItemShow[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchItems = async () => {
      try {
        setIsLoading(true);
        setError(null);

        const { data } = await axios.get(`/api/v1/mylist/${listId}/items`);
        if (Array.isArray(data.data)) {
          setItems(data.data);
        } else {
          throw new Error("アイテムの取得に失敗しました");
        }
      } catch (err) {
        setError(extractErrorMessage(err));
      } finally {
        setIsLoading(false);
      }
    };

    useEffect(() => {
      fetchItems();
    }, []);

    const handleUpdate = () => {
      fetchItems();
    };

    return (
      <>
        {/* エラー表示 */}
        {error && (
          <div className="v2-card v2-card-danger mb-6" role="alert">
            <p className="v2-text-body">{error}</p>
          </div>
        )}

        {/* アイテム一覧 */}
        {isLoading ? (
          <div className="v2-card v2-card-main">
            <div className="v2-text-center py-12">
              <p className="v2-text-body text-gray-500">読み込み中...</p>
            </div>
          </div>
        ) : (
          <MyListItemsTable
            items={items}
            listId={listId}
            onUpdate={handleUpdate}
          />
        )}
      </>
    );
  };

  createRoot(app).render(
    <AppWrapper boundaryName="MyListDetailPage">
      <App />
    </AppWrapper>
  );
}
