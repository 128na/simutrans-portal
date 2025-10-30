import ButtonClose from "@/apps/components/ui/ButtonClose";

type Props = {
  article: ListingArticle | null;
  onClose?: () => void;
};

export const ArticleModal = ({ article, onClose }: Props) => {
  // article が null の場合はモーダルを非表示にする
  if (!article) return null;

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
      aria-modal="true"
      role="dialog"
    >
      <div className="relative p-4 w-full max-w-2xl">
        <div className="bg-white rounded-lg shadow p-5">
          <div className="flex justify-between items-center pb-3 mb-4">
            <h3 className="text-lg font-semibold text-gray-900 ">
              {article.title}
            </h3>
            <ButtonClose onClick={onClose} />
          </div>

          <div className="grid gap-x-4 mb-4">ここに操作</div>
        </div>
      </div>
    </div>
  );
};
