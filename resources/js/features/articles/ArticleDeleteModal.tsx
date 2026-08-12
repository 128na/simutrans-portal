import axios from "axios";
import { Modal } from "@/components/ui/Modal";
import Button from "@/components/ui/Button";
import { useApiCall } from "@/hooks/useApiCall";

interface ArticleDeleteModalProps {
  article: Article.MypageShow | null;
  onClose: () => void;
  onSuccess: () => void;
}

/**
 * 記事削除確認モーダル
 */
export const ArticleDeleteModal = ({
  article,
  onClose,
  onSuccess,
}: ArticleDeleteModalProps) => {
  const { call, isLoading } = useApiCall();

  const handleDelete = async () => {
    if (!article) return;

    await call(() => axios.delete(`/api/v2/articles/${article.id}`), {
      successMessage: "記事を削除しました",
      onSuccess: () => onSuccess(),
    });
  };

  if (!article) {
    return null;
  }

  return (
    <Modal title="記事を削除" onClose={onClose}>
      <p className="v2-text-body mb-4">
        「<strong>{article.title}</strong>」を削除してもよろしいですか？
      </p>
      <p className="text-sm v2-text-sub mb-6">
        この操作は取り消せません。
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
