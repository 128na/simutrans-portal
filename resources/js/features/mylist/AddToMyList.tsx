import axios from "axios";
import { useState, useEffect } from "react";
import Button from "@/components/ui/Button";
import { Modal } from "@/components/ui/Modal";
import Input from "@/components/ui/Input";
import TextBadge from "@/components/ui/TextBadge";
import { useApiCall } from "@/hooks/useApiCall";
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
      <Button
        variant="sub"
        onClick={handleClick}
        className={className}
        aria-label="マイリストに追加"
      >
        <span className="icon-plus"></span>
        マイリスト
      </Button>

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
  const { call } = useApiCall();
  const [lists, setLists] = useState<MyListShow[]>([]);
  const [selectedListIds, setSelectedListIds] = useState<Set<number>>(
    new Set()
  );
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isCreating, setIsCreating] = useState(false);
  const [newListTitle, setNewListTitle] = useState("");

  // リスト一覧を取得
  useEffect(() => {
    const fetchLists = async () => {
      try {
        setIsLoading(true);
        const { data } = await axios.get("/api/v1/mylist");
        if (Array.isArray(data.data)) {
          setLists(data.data);
        } else {
          throw new Error("リストの取得に失敗しました");
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : "エラーが発生しました");
      } finally {
        setIsLoading(false);
      }
    };

    fetchLists();
  }, []);

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

    setError(null);
    setIsCreating(true);

    const requestBody: MyListCreateRequest = {
      title: newListTitle.trim(),
    };

    const result = await call(() => axios.post("/api/v1/mylist", requestBody), {
      successMessage: "マイリストを作成しました",
      onSuccess: (response) => {
        if (response.data && typeof response.data === "object") {
          setLists([...lists, response.data]);
          setNewListTitle("");
        }
      },
    });

    setIsCreating(false);

    if (result.hasError && !result.validationErrors) {
      setError("リストの作成に失敗しました");
    }

    // バリデーションエラーがある場合は表示
    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
    }
  };

  const handleAddToLists = async () => {
    if (selectedListIds.size === 0) {
      setError("リストを選択してください");
      return;
    }

    setError(null);

    const result = await call(
      async () => {
        const promises = Array.from(selectedListIds).map(async (listId) => {
          try {
            await axios.post(`/api/v1/mylist/${listId}/items`, {
              article_id: articleId,
            });
          } catch (err) {
            // 409 は重複なので握りつぶす
            if (!axios.isAxiosError(err) || err.response?.status !== 409) {
              throw err;
            }
          }
        });

        await Promise.all(promises);
      },
      {
        successMessage: "マイリストに追加しました",
        onSuccess: () => onSuccess(),
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
    <Modal title="マイリストに追加" onClose={onClose}>
      {error && (
        <div className="v2-card v2-card-danger mb-4" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      {isLoading ? (
        <div className="v2-text-center py-8">
          <p className="v2-text-body text-gray-500">読み込み中...</p>
        </div>
      ) : lists.length === 0 ? (
        <div className="v2-text-center py-8">
          <p className="v2-text-body text-gray-500">
            マイリストがありません。新しく作成してください。
          </p>
        </div>
      ) : (
        <div className="space-y-2 mb-6 max-h-60 overflow-y-auto">
          {lists.map((list) => (
            <label
              key={list.id}
              className="flex items-center gap-3 p-3 border v2-border-sub rounded hover:bg-gray-50 cursor-pointer"
            >
              <input
                type="checkbox"
                checked={selectedListIds.has(list.id)}
                onChange={() => handleToggleList(list.id)}
                className="v2-checkbox"
              />
              <div className="flex-1 min-w-0">
                <div className="font-medium truncate">{list.title}</div>
                {list.note && (
                  <div className="text-sm v2-text-sub mt-1 line-clamp-2">
                    {list.note}
                  </div>
                )}
              </div>
              {list.is_public && <TextBadge variant="primary">公開</TextBadge>}
            </label>
          ))}
        </div>
      )}

      <div className="v2-divider pt-4">
        <h4 className="font-medium mb-3">新しいリストを作成</h4>
        <div className="flex flex-col sm:flex-row gap-2">
          <Input
            value={newListTitle}
            onChange={(e) => setNewListTitle(e.target.value)}
            placeholder="リストのタイトル"
            maxLength={120}
            disabled={isCreating}
            className="flex-1"
          />
          <Button
            onClick={handleCreateList}
            disabled={isCreating || !newListTitle.trim()}
            variant="sub"
            className="w-full sm:w-auto"
          >
            {isCreating ? "作成中..." : "作成"}
          </Button>
        </div>
      </div>

      <div className="flex gap-2 justify-end mt-6">
        <Button onClick={onClose} disabled={isLoading} variant="subOutline">
          キャンセル
        </Button>
        <Button
          onClick={handleAddToLists}
          disabled={isLoading || selectedListIds.size === 0}
          variant="primary"
        >
          {isLoading ? "追加中..." : "追加"}
        </Button>
      </div>
    </Modal>
  );
};
