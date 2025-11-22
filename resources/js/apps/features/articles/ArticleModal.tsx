import { Modal } from "@/apps/components/ui/Modal";

type Props = {
  user: MypageArticleListUser;
  article: MypageArticleListArticle | null;
  onClose?: () => void;
};

export const ArticleModal = ({ user, article, onClose }: Props) => {
  // article が null の場合はモーダルを非表示にする
  if (!article) return null;

  const editUrl = `${import.meta.env.VITE_APP_URL}/mypage/articles/edit/${article.id}`;
  const showUrl = `${import.meta.env.VITE_APP_URL}/users/${user.nickname ?? user.id}/${decodeURI(article.slug)}`;

  const onCopyClick = async () => {
    await navigator.clipboard.writeText(showUrl);
    alert("コピーしました");
  };

  return (
    <Modal title={article.title} onClose={onClose}>
      <div className="grid gap-x-4 mb-4">
        <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
          <div className="flex-auto">
            <a href={editUrl} className="block font-semibold text-gray-900">
              編集する
              <span className="absolute inset-0"></span>
            </a>
          </div>
        </div>
        <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
          <div className="flex-auto">
            <a
              href={showUrl}
              className="block font-semibold text-gray-900"
              target="_blank"
              rel="noopener noreferrer"
            >
              記事を表示
              <span className="absolute inset-0"></span>
            </a>
          </div>
        </div>
        <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
          <div className="flex-auto">
            <button
              className="block font-semibold text-gray-900 cursor-pointer"
              onClick={() => onCopyClick()}
            >
              URLをコピー
              <span className="absolute inset-0"></span>
            </button>
          </div>
        </div>
      </div>
    </Modal>
  );
};
