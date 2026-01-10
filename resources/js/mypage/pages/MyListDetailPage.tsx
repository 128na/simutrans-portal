import { createRoot } from "react-dom/client";
import { useState, useEffect } from "react";
import { ErrorBoundary } from "../../components/ErrorBoundary";
import { MyListItemsTable } from "../../features/mylist/MyListItemsTable";
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

        const response = await fetch(`/api/v1/mylist/${list.id}/items`, {
          credentials: "include",
        });

        if (!response.ok) {
          throw new Error("アイテムの取得に失敗しました");
        }

        const data = await response.json();
        if (data.ok && data.data?.items) {
          setItems(data.data.items);
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
        <div className="mb-8">
          <div className="flex items-center gap-4 mb-4">
            <a href="/mypage/mylists" className="btn btn-secondary btn-sm">
              ← マイリスト一覧へ
            </a>
            {list.is_public && list.slug && (
              <a
                href={`/mylist/${list.slug}`}
                className="btn btn-secondary btn-sm"
                target="_blank"
                rel="noopener noreferrer"
              >
                公開ページを表示
              </a>
            )}
          </div>

          <h2 className="v2-text-h2 mb-2">{list.title}</h2>
          {list.note && (
            <p className="text-gray-600 whitespace-pre-wrap">{list.note}</p>
          )}
          <div className="flex gap-2 mt-2">
            {list.is_public ? (
              <span className="badge badge-success">公開</span>
            ) : (
              <span className="badge badge-secondary">非公開</span>
            )}
          </div>
        </div>

        {error && (
          <div className="alert alert-danger mb-6" role="alert">
            {error}
          </div>
        )}

        {isLoading ? (
          <div className="text-center py-12">読み込み中...</div>
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
