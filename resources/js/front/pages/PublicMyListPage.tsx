import axios from "axios";
import { useEffect, useState } from "react";
import ReactDOM from "react-dom/client";
import TextBadge from "@/components/ui/TextBadge";
import type { MyListShow, MyListItemShow } from "@/types/models";

/**
 * 公開マイリスト表示ページ
 */
const PublicMyListPage = ({
  mylist: initialMyList,
}: {
  mylist: {
    id: number;
    title: string;
    note: string | null;
    is_public: boolean;
    slug: string;
    created_at: string;
    updated_at: string;
  };
}) => {
  const mylist = initialMyList as unknown as MyListShow;
  const [items, setItems] = useState<MyListItemShow[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchItems = async () => {
      try {
        const { data } = await axios.get(
          `/api/v1/mylist/public/${mylist.slug}`
        );
        if (data.ok && data.data?.items) {
          setItems(data.data.items || []);
        } else {
          throw new Error("アイテムの取得に失敗しました");
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : "エラーが発生しました");
      } finally {
        setIsLoading(false);
      }
    };

    fetchItems();
  }, [mylist.slug]);

  return (
    <div>
      {/* ヘッダー情報 */}
      <div className="v2-card v2-card-default mb-6">
        <h1 className="v2-text-h2 mb-3">
          <TextBadge variant="success" className="mr-2">
            公開
          </TextBadge>
          {mylist.title}
        </h1>
        {mylist.note && (
          <p className="v2-text-body text-gray-600 whitespace-pre-wrap mb-3">
            {mylist.note}
          </p>
        )}
        <p className="text-sm v2-text-sub">
          更新日: {new Date(mylist.updated_at).toLocaleDateString("ja-JP")}
        </p>
      </div>

      {/* エラー表示 */}
      {error && (
        <div className="v2-card v2-card-danger mb-6" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      {/* アイテム一覧 */}
      {isLoading ? (
        <div className="v2-card v2-card-default">
          <p className="v2-text-body text-gray-500">読み込み中...</p>
        </div>
      ) : items.length === 0 ? (
        <div className="v2-card v2-card-default">
          <p className="v2-text-body text-gray-500">アイテムがありません</p>
        </div>
      ) : (
        <div className="space-y-4">
          {items.map((item) => (
            <div key={item.id} className="v2-card v2-card-default">
              <div className="flex gap-4">
                {/* サムネイル */}
                <div className="flex-shrink-0 w-24 h-24">
                  {item.article &&
                  "thumbnail" in item.article &&
                  item.article.thumbnail ? (
                    <img
                      src={item.article.thumbnail}
                      alt={item.article.title}
                      className="w-full h-full object-cover rounded"
                    />
                  ) : (
                    <div className="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                      <span className="text-gray-400 text-sm">No Image</span>
                    </div>
                  )}
                </div>

                {/* 記事情報 */}
                <div className="flex-1 min-w-0">
                  <h3 className="v2-text-h4 mb-2 truncate">
                    {item.article && "url" in item.article ? (
                      <a
                        href={item.article.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-blue-600 hover:underline"
                      >
                        {item.article.title}
                      </a>
                    ) : (
                      <span>{item.article?.title}</span>
                    )}
                  </h3>

                  {/* 投稿者情報 */}
                  {item.article && "user" in item.article && (
                    <p className="text-sm v2-text-sub mb-2">
                      投稿者: {item.article.user?.name || "不明"}
                    </p>
                  )}

                  {/* 追加日 */}
                  <p className="text-sm v2-text-sub mb-2">
                    追加日:{" "}
                    {new Date(item.created_at).toLocaleDateString("ja-JP")}
                  </p>

                  {/* メモ */}
                  {item.note && (
                    <div className="bg-yellow-50 border border-yellow-200 rounded p-2 mt-2">
                      <p className="text-sm text-gray-700 break-words">
                        {item.note}
                      </p>
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

// Reactマウント
const script = document.getElementById("data-public-mylist");
if (script) {
  const mylist = JSON.parse(script.textContent || "{}");
  const container = document.getElementById("app-public-mylist");
  if (container) {
    const root = ReactDOM.createRoot(container);
    root.render(<PublicMyListPage mylist={mylist} />);
  }
}
