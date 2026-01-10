import axios from "axios";
import { useEffect, useState } from "react";
import ReactDOM from "react-dom/client";
import type { MyListItemShow } from "@/types/models";

/**
 * 公開マイリスト表示ページ
 */
const PublicMyListPage = ({ mylistSlug }: { mylistSlug: string }) => {
  const [items, setItems] = useState<MyListItemShow[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchItems = async () => {
      try {
        const { data } = await axios.get(`/api/v1/mylist/public/${mylistSlug}`);
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
  }, [mylistSlug]);

  return (
    <div>
      {/* エラー表示 */}
      {error && (
        <div className="v2-card v2-card-danger mb-6" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      {/* アイテム一覧 */}
      {isLoading ? (
        <div className="v2-card v2-card-main">
          <p className="v2-text-body v2-text-sub">読み込み中...</p>
        </div>
      ) : items.length === 0 ? (
        <div className="v2-card v2-card-main">
          <p className="v2-text-body v2-text-sub">アイテムがありません</p>
        </div>
      ) : (
        <div className="space-y-4">
          {items.map((item) => (
            <div key={item.id} className="v2-card v2-card-main">
              <div className="flex gap-4">
                {/* サムネイル */}
                <div className="shrink-0 w-24 h-24">
                  {item.article &&
                  "thumbnail" in item.article &&
                  item.article.thumbnail ? (
                    <img
                      src={item.article.thumbnail}
                      alt={item.article.title}
                      className="w-full h-full object-cover rounded"
                    />
                  ) : (
                    <div className="w-full h-full v2-card v2-card-sub p-0 flex items-center justify-center">
                      <span className="v2-text-sub text-sm">No Image</span>
                    </div>
                  )}
                </div>

                {/* 記事情報 */}
                <div className="flex-1 min-w-0">
                  <h3 className="v2-text-h4 mb-2 truncate">
                    {item.article &&
                    "url" in item.article &&
                    item.article.url ? (
                      <a
                        href={item.article.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="v2-link"
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
                      <p className="text-sm text-gray-700 wrap-break-word">
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
const container = document.getElementById("app-public-mylist");
if (container) {
  const mylistSlug = container.getAttribute("data-mylist-slug");
  if (!mylistSlug) {
    throw new Error("MyList slug is not provided");
  }
  const root = ReactDOM.createRoot(container);
  root.render(<PublicMyListPage mylistSlug={mylistSlug} />);
}
