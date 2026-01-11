import axios from "axios";
import { useState } from "react";
import Button from "@/components/ui/Button";
import { Modal } from "@/components/ui/Modal";
import Input from "@/components/ui/Input";
import Textarea from "@/components/ui/Textarea";
import Checkbox from "@/components/ui/Checkbox";
import TextBadge from "@/components/ui/TextBadge";
import Link from "@/components/ui/Link";
import { copyToClipboard } from "@/lib/copyText";
import { useToast } from "@/hooks/useToast";
import { useApiCall } from "@/hooks/useApiCall";
import { useModelModal } from "@/hooks/useModelModal";
import type { MyListShow } from "@/types/models";

interface MyListTableProps {
  lists: MyListShow[];
  onEdit: (list: MyListShow) => void;
  onDelete: (list: MyListShow) => void;
}

/**
 * マイリスト一覧テーブルコンポーネント
 */
export const MyListTable = ({ lists, onEdit, onDelete }: MyListTableProps) => {
  const { showSuccess } = useToast();
  const [copiedId, setCopiedId] = useState<number | null>(null);

  const handleCopyPublicUrl = async (list: MyListShow) => {
    const publicUrl = `${window.location.origin}/mylist/${list.slug}`;
    const success = await copyToClipboard(publicUrl);

    if (success) {
      setCopiedId(list.id);
      showSuccess("公開URLをコピーしました");
      setTimeout(() => setCopiedId(null), 2000);
    } else {
      // showError はここでは不要（copyToClipboardが失敗時alert表示）
    }
  };

  if (lists.length === 0) {
    return (
      <div className="v2-card v2-card-main">
        <div className="v2-text-center py-12">
          <p className="v2-text-body text-gray-500">
            マイリストがありません。新しく作成してください。
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="v2-table-wrapper">
      <table className="v2-table v2-table-fixed">
        <thead>
          <tr>
            <th className="w-5/12">タイトル</th>
            <th className="w-2/12">アイテム数</th>
            <th className="w-2/12">更新日</th>
            <th className="w-3/12">操作</th>
          </tr>
        </thead>
        <tbody>
          {lists.map((list) => (
            <tr key={list.id}>
              <td>
                <div>
                  {list.is_public ? (
                    <TextBadge variant="success" className="mr-2">
                      公開
                    </TextBadge>
                  ) : (
                    <TextBadge variant="sub" className="mr-2">
                      非公開
                    </TextBadge>
                  )}
                  <Link
                    href={`/mypage/mylists/${list.id}`}
                    className="font-medium"
                  >
                    {list.title}
                  </Link>
                </div>
                {list.note && (
                  <div className="text-sm v2-text-sub mt-1 line-clamp-2">
                    {list.note}
                  </div>
                )}
              </td>
              <td>{list.items_count || 0}件</td>
              <td>{new Date(list.updated_at).toLocaleDateString("ja-JP")}</td>
              <td>
                <div className="flex gap-2 justify-end flex-wrap">
                  {list.is_public && (
                    <Button
                      onClick={() => handleCopyPublicUrl(list)}
                      variant={copiedId === list.id ? "success" : "subOutline"}
                      aria-label={`${list.title}の公開URLをコピー`}
                      title="公開URLをクリップボードにコピーします"
                    >
                      {copiedId === list.id ? (
                        <>
                          <span className="icon-check"></span>
                          コピー済
                        </>
                      ) : (
                        <>
                          <span className="icon-copy"></span>
                          URLコピー
                        </>
                      )}
                    </Button>
                  )}
                  <Button
                    onClick={() => onEdit(list)}
                    variant="primary"
                    aria-label={`${list.title}を編集`}
                  >
                    編集
                  </Button>
                  <Button
                    onClick={() => onDelete(list)}
                    variant="danger"
                    aria-label={`${list.title}を削除`}
                  >
                    削除
                  </Button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

interface MyListEditModalProps {
  list: MyListShow | null;
  onClose: () => void;
  onSuccess: () => void;
}

/**
 * マイリスト編集モーダル
 */
export const MyListEditModal = ({
  list,
  onClose,
  onSuccess,
}: MyListEditModalProps) => {
  const { showSuccess } = useToast();
  const { isLoading, error, getError, handleSave } = useModelModal();
  const [title, setTitle] = useState(list?.title || "");
  const [note, setNote] = useState(list?.note || "");
  const [isPublic, setIsPublic] = useState(list?.is_public || false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!title.trim()) {
      return;
    }

    const method = list ? "PATCH" : "POST";
    const url = list ? `/api/v1/mylist/${list.id}` : "/api/v1/mylist";

    await handleSave(
      () =>
        axios({
          method,
          url,
          data: {
            title: title.trim(),
            note: note.trim() || null,
            is_public: isPublic,
          },
        }),
      {
        successMessage: list
          ? "マイリストを更新しました"
          : "マイリストを作成しました",
        onSuccess: () => {
          showSuccess(
            list ? "マイリストを更新しました" : "マイリストを作成しました"
          );
          onSuccess();
        },
      }
    );
  };

  if (!list && list !== null) {
    return null;
  }

  return (
    <Modal
      title={list ? "マイリストを編集" : "マイリストを作成"}
      onClose={onClose}
    >
      {error && (
        <div className="v2-card v2-card-danger mb-4" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4 mb-6">
        <div>
          <label htmlFor="title" className="v2-form-caption">
            <TextBadge variant="danger">必須</TextBadge>
            タイトル
          </label>
          <Input
            id="title"
            className="w-full"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            maxLength={120}
            required
            disabled={isLoading}
          />
        </div>

        <div>
          <label htmlFor="note" className="v2-form-caption">
            メモ
          </label>
          <Textarea
            id="note"
            className="w-full"
            value={note}
            onChange={(e) => setNote(e.target.value)}
            rows={4}
            disabled={isLoading}
          />
        </div>

        <div className="flex items-center gap-2">
          <Checkbox
            checked={isPublic}
            onChange={(e) => setIsPublic(e.target.checked)}
            disabled={isLoading}
          >
            このリストを公開する
          </Checkbox>
        </div>
        <div className="text-sm v2-text-sub">
          公開すると、URLを知っている人が閲覧できます
        </div>

        <div className="flex gap-2 justify-end mt-6">
          <Button
            type="button"
            onClick={onClose}
            variant="subOutline"
            disabled={isLoading}
          >
            キャンセル
          </Button>
          <Button
            type="submit"
            variant="primary"
            disabled={isLoading || !title.trim()}
          >
            {isLoading ? "保存中..." : "保存"}
          </Button>
        </div>
      </form>
    </Modal>
  );
};

interface MyListDeleteModalProps {
  list: MyListShow | null;
  onClose: () => void;
  onSuccess: () => void;
}

/**
 * マイリスト削除確認モーダル
 */
export const MyListDeleteModal = ({
  list,
  onClose,
  onSuccess,
}: MyListDeleteModalProps) => {
  const { call, isLoading } = useApiCall();
  const [error, setError] = useState<string | null>(null);

  const handleDelete = async () => {
    if (!list) return;

    setError(null);

    const result = await call(() => axios.delete(`/api/v1/mylist/${list.id}`), {
      successMessage: "マイリストを削除しました",
      onSuccess: () => onSuccess(),
    });

    // バリデーションエラーがある場合は表示
    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
    }
  };

  if (!list) {
    return null;
  }

  return (
    <Modal title="マイリストを削除" onClose={onClose}>
      {error && (
        <div className="v2-card v2-card-danger mb-4" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      <p className="v2-text-body mb-4">
        「<strong>{list.title}</strong>」を削除してもよろしいですか？
      </p>
      <p className="text-sm v2-text-sub mb-6">
        この操作は取り消せません。リスト内のアイテムもすべて削除されます。
      </p>

      <div className="flex gap-2 justify-end">
        <Button onClick={onClose} variant="subOutline" disabled={isLoading}>
          キャンセル
        </Button>
        <Button onClick={handleDelete} variant="danger" disabled={isLoading}>
          {isLoading ? "削除中..." : "削除"}
        </Button>
      </div>
    </Modal>
  );
};
