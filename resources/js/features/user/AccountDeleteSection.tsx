import { useState } from "react";
import TextBadge from "@/components/ui/TextBadge";
import Button from "@/components/ui/Button";
import { AccountDeleteModal } from "./AccountDeleteModal";

/**
 * 退会（アカウント削除）セクション
 */
export const AccountDeleteSection = () => {
  const [showModal, setShowModal] = useState(false);

  return (
    <div className="v2-divider pt-4 mt-4">
      <TextBadge variant="danger">危険な操作</TextBadge>
      <p className="v2-text-body mt-2 mb-4">
        アカウントを削除すると、投稿した記事はすべて非公開になり、ログインできなくなります。
      </p>
      <Button variant="dangerOutline" onClick={() => setShowModal(true)}>
        退会する
      </Button>

      {showModal && (
        <AccountDeleteModal onClose={() => setShowModal(false)} />
      )}
    </div>
  );
};
