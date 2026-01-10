import { createRoot } from "react-dom/client";
import { useState, useEffect } from "react";
import { ErrorBoundary } from "../../components/ErrorBoundary";
import {
  MyListTable,
  MyListEditModal,
  MyListDeleteModal,
} from "../../features/mylist/MyListTable";
import type { MyListShow } from "@/types/models";

const app = document.getElementById("app-mylist-index");

if (app) {
  const App = () => {
    const [lists, setLists] = useState<MyListShow[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [editingList, setEditingList] = useState<MyListShow | null>(null);
    const [deletingList, setDeletingList] = useState<MyListShow | null>(null);
    const [isCreating, setIsCreating] = useState(false);

    const fetchLists = async () => {
      try {
        setIsLoading(true);
        setError(null);

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

    useEffect(() => {
      fetchLists();
    }, []);

    const handleEdit = (list: MyListShow) => {
      setEditingList(list);
    };

    const handleDelete = (list: MyListShow) => {
      setDeletingList(list);
    };

    const handleCreate = () => {
      setIsCreating(true);
    };

    const handleSuccess = () => {
      setEditingList(null);
      setDeletingList(null);
      setIsCreating(false);
      fetchLists();
    };

    const handleCloseEdit = () => {
      setEditingList(null);
      setIsCreating(false);
    };

    const handleCloseDelete = () => {
      setDeletingList(null);
    };

    return (
      <div className="v2-page v2-page-lg">
        <div className="mb-12 flex justify-between items-center">
          <h2 className="v2-text-h2">マイリスト</h2>
          <button
            type="button"
            onClick={handleCreate}
            className="btn btn-primary"
          >
            <span className="icon-plus"></span>
            新しいリストを作成
          </button>
        </div>

        {error && (
          <div className="alert alert-danger mb-6" role="alert">
            {error}
          </div>
        )}

        {isLoading ? (
          <div className="text-center py-12">読み込み中...</div>
        ) : (
          <MyListTable
            lists={lists}
            onEdit={handleEdit}
            onDelete={handleDelete}
          />
        )}

        {(editingList || isCreating) && (
          <MyListEditModal
            list={editingList}
            onClose={handleCloseEdit}
            onSuccess={handleSuccess}
          />
        )}

        {deletingList && (
          <MyListDeleteModal
            list={deletingList}
            onClose={handleCloseDelete}
            onSuccess={handleSuccess}
          />
        )}
      </div>
    );
  };

  createRoot(app).render(
    <ErrorBoundary name="MyListIndexPage">
      <App />
    </ErrorBoundary>
  );
}
