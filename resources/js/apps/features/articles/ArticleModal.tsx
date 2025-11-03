import ButtonClose from "@/apps/components/ui/ButtonClose";

type Props = {
  user: User.WithRole;
  article: Article.Listing | null;
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
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
      aria-modal="true"
      role="dialog"
    >
      <div className="relative p-4 w-full max-w-md">
        <div className="bg-white rounded-lg shadow p-5">
          <div className="flex justify-between items-center pb-3 mb-4">
            <h3 className="text-lg font-semibold text-gray-900 ">
              {article.title}
            </h3>
            <ButtonClose onClick={onClose} />
          </div>
          <div className="grid gap-x-4 mb-4">
            <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
              <div className="flex-auto">
                <a href={editUrl} className="block font-semibold text-gray-900">
                  編集
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
                  記事表示
                  <span className="absolute inset-0"></span>
                </a>
              </div>
            </div>
            <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
              <div className="flex-auto">
                <button
                  className="block font-semibold text-gray-900"
                  onClick={() => onCopyClick()}
                >
                  URLコピー
                  <span className="absolute inset-0"></span>
                </button>
              </div>
            </div>
            {/* <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
              <div className="flex-auto">
                <a href="#" className="block font-semibold text-gray-900">
                  公開切り替え
                  <span className="absolute inset-0"></span>
                </a>
              </div>
            </div> */}
          </div>
        </div>
      </div>
    </div>
  );
};
