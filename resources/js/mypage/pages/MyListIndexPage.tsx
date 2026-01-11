import axios from "axios";
import { createRoot } from "react-dom/client";
import { useState, useEffect } from "react";
import Button from "@/components/ui/Button";
import { AppWrapper } from "../../components/AppWrapper";
import { extractErrorMessage } from "@/lib/errorHandler";
import {
  MyListTable,
  MyListEditModal,
  MyListDeleteModal,
} from "../../features/mylist/MyListTable";
import type { MyListShow } from "@/types/models";

const app = document.getElementById("app-mylist-index");

if (app) {
  const App = () => {
    // 成功メッセージはモーダル内から showSuccess() で出力される
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

        const { data } = await axios.get("/api/v1/mylist");
        if (Array.isArray(data.data)) {
          setLists(data.data);
        } else {
          throw new Error("リストの取得に失敗しました");
        }
      } catch (err) {
        setError(extractErrorMessage(err));
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
      // 成功メッセージはモーダル内から呼び出される
    };

    const handleCloseEdit = () => {
      setEditingList(null);
      setIsCreating(false);
    };

    const handleCloseDelete = () => {
      setDeletingList(null);
    };

    return (
      <>
        <div className="mb-6">
          <Button onClick={handleCreate} variant="primary" size="lg">
            マイリストを作成
          </Button>
        </div>

        {error && (
          <div className="v2-card v2-card-danger mb-6" role="alert">
            <p className="v2-text-body">{error}</p>
          </div>
        )}

        {isLoading ? (
          <div className="v2-card v2-card-main">
            <div className="v2-text-center py-12">
              <p className="v2-text-body text-gray-500">読み込み中...</p>
            </div>
          </div>
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
      </>
    );
  };

  createRoot(app).render(
    <AppWrapper boundaryName="MyListIndexPage">
      <App />
    </AppWrapper>
  );
}
