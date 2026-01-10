import axios from "axios";
import { createRoot } from "react-dom/client";
import { useState, useEffect } from "react";
import { ErrorBoundary } from "../../components/ErrorBoundary";
import { MyListItemsTable } from "../../features/mylist/MyListItemsTable";
import TextBadge from "../../components/ui/TextBadge";
import Link from "../../components/ui/Link";
import type { MyListShow, MyListItemShow } from "@/types/models";

const app = document.getElementById("app-mylist-detail");

if (app) {
  const listData = JSON.parse(
    document.getElementById("data-mylist")?.textContent || "{}"
  ) as MyListShow;

  const App = () => {
    const [list] = useState<MyListShow>(listData);
    const [items, setItems] = useState<MyListItemShow[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchItems = async () => {
      try {
        setIsLoading(true);
        setError(null);

        const { data } = await axios.get(`/api/v1/mylist/${list.id}/items`);
        if (data.ok && data.data?.items) {
          setItems(data.data.items);
        } else {
          throw new Error("アイテムの取得に失敗しました");
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : "エラーが発生しました");
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
      <div className="v2-page v2-page-lg">
        {/* ナビゲーション */}
        <div className="flex items-center gap-3 mb-6">
          <Link href="/mypage/mylists">← マイリスト一覧へ</Link>
          {list.is_public && list.slug && (
            <Link href={`/mylist/${list.slug}`}>公開ページを表示</Link>
          )}
        </div>

        {/* ヘッダー情報 */}
        <div className="v2-card v2-card-default mb-6">
          <h1 className="v2-text-h2 mb-3">
            <TextBadge variant={list.is_public ? "success" : "secondary"}>
              {list.is_public ? "公開" : "非公開"}
            </TextBadge>
            {list.title}
          </h1>
          {list.note && (
            <p className="v2-text-body text-gray-600 whitespace-pre-wrap mb-3">
              {list.note}
            </p>
          )}
        </div>

        {/* エラー表示 */}
        {error && (
          <div className="v2-card v2-card-danger mb-6" role="alert">
            <p className="v2-text-body">{error}</p>
          </div>
        )}

        {/* アイテム一覧 */}
        {isLoading ? (
          <div className="v2-card v2-card-default">
            <div className="v2-text-center py-12">
              <p className="v2-text-body text-gray-500">読み込み中...</p>
            </div>
          </div>
        ) : (
          <MyListItemsTable
            items={items}
            listId={list.id}
            onUpdate={handleUpdate}
          />
        )}
      </div>
    );
  };

  createRoot(app).render(
    <ErrorBoundary name="MyListDetailPage">
      <App />
    </ErrorBoundary>
  );
}
