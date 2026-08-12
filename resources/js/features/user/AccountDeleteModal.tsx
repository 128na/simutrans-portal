import axios from "axios";
import { useState } from "react";
import { Modal } from "@/components/ui/Modal";
import Button from "@/components/ui/Button";
import Input from "@/components/ui/Input";
import { useApiCall } from "@/hooks/useApiCall";

interface AccountDeleteModalProps {
  onClose: () => void;
}

/**
 * 退会確認モーダル
 */
export const AccountDeleteModal = ({ onClose }: AccountDeleteModalProps) => {
  const { call, isLoading } = useApiCall();
  const [currentPassword, setCurrentPassword] = useState("");
  const [error, setError] = useState<string | null>(null);

  const handleWithdraw = async () => {
    setError(null);

    const result = await call(
      () =>
        axios.delete("/mypage/account", {
          data: { current_password: currentPassword },
        }),
      {
        onSuccess: () => {
          window.location.href = "/login";
        },
      }
    );

    if (result.validationErrors) {
      const errorMessages = Object.values(result.validationErrors)
        .flat()
        .join("\n");
      setError(errorMessages);
    }
  };

  return (
    <Modal title="退会する" onClose={onClose}>
      {error && (
        <div className="v2-card v2-card-danger mb-4" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      <p className="v2-text-body mb-4">
        退会すると投稿した記事はすべて非公開になり、ログインできなくなります。この操作は取り消せません。
      </p>

      <label className="block mb-6">
        <span className="v2-text-body block mb-1">パスワード</span>
        <Input
          type="password"
          className="w-full"
          value={currentPassword}
          onChange={(e) => setCurrentPassword(e.target.value)}
          autoComplete="current-password"
          required
        />
      </label>

      <div className="flex gap-2 justify-end">
        <Button onClick={onClose} variant="subOutline" disabled={isLoading}>
          キャンセル
        </Button>
        <Button
          onClick={handleWithdraw}
          variant="danger"
          disabled={isLoading || !currentPassword}
        >
          {isLoading ? "退会処理中..." : "退会する"}
        </Button>
      </div>
    </Modal>
  );
};
