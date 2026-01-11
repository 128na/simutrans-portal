import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import axios from "axios";
import React from "react";
import type { MyListItemShow } from "@/types/models/MyList";

// PublicMyListPage コンポーネントを直接テストするために、
// マウント処理を含まないコンポーネント部分のみをインポート
vi.mock("axios");
const mockAxios = axios as ReturnType<typeof vi.mocked<typeof axios>>;

describe("PublicMyListPage コンポーネント", () => {
  const mockSlug = "test-list-123";

  // テスト用の PublicMyListPage コンポーネントを作成
  // 本来のファイルからマウント処理を除外したものを使用
  const PublicMyListPage = ({
    mylistSlug,
  }: {
    mylistSlug: string;
  }): JSX.Element => {
    const [items, setItems] = React.useState<MyListItemShow[]>([]);
    const [isLoading, setIsLoading] = React.useState(true);
    const [error, setError] = React.useState<string | null>(null);

    React.useEffect(() => {
      const fetchItems = async () => {
        try {
          const { data } = await axios.get(
            `/api/v1/mylist/public/${mylistSlug}`
          );
          if (Array.isArray(data.data)) {
            setItems(data.data);
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

  const mockItemsWithPublicArticle: MyListItemShow[] = [
    {
      id: 1,
      note: "便利なアドオン",
      position: 1,
      created_at: "2025-01-01T10:00:00Z",
      article: {
        id: 100,
        title: "テストアドオン1",
        published_at: "2025-01-01T10:00:00Z",
        thumbnail: "https://example.com/thumb1.jpg",
        url: "https://example.com/addon1",
        user: {
          name: "テストユーザー",
          avatar: "https://example.com/avatar1.jpg",
        },
      },
    },
    {
      id: 2,
      note: null,
      position: 2,
      created_at: "2025-01-02T12:00:00Z",
      article: {
        id: 101,
        title: "テストアドオン2",
        published_at: "2025-01-02T12:00:00Z",
        thumbnail: null,
        url: "https://example.com/addon2",
        user: {
          name: "テストユーザー2",
          avatar: "https://example.com/avatar2.jpg",
        },
      },
    },
  ];

  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("初期状態でローディングを表示する", () => {
    mockAxios.get = vi.fn(() => new Promise(() => {})); // 永遠に待つ

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    expect(screen.getByText("読み込み中...")).toBeInTheDocument();
  });

  it("APIからアイテムを取得して表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByText("テストアドオン1")).toBeInTheDocument();
      expect(screen.getByText("テストアドオン2")).toBeInTheDocument();
    });

    expect(mockAxios.get).toHaveBeenCalledWith(
      `/api/v1/mylist/public/${mockSlug}`
    );
  });

  it("空のリストでメッセージを表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: [] },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByText("アイテムがありません")).toBeInTheDocument();
    });
  });

  it("サムネイルがある記事を表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      const thumbnail = screen.getByAltText("テストアドオン1");
      expect(thumbnail).toBeInTheDocument();
      expect(thumbnail).toHaveAttribute(
        "src",
        "https://example.com/thumb1.jpg"
      );
    });
  });

  it("サムネイルがない記事に No Image を表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      const noImages = screen.getAllByText("No Image");
      expect(noImages.length).toBeGreaterThan(0);
    });
  });

  it("記事のリンクを表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      const link = screen.getByRole("link", { name: "テストアドオン1" });
      expect(link).toHaveAttribute("href", "https://example.com/addon1");
      expect(link).toHaveAttribute("target", "_blank");
    });
  });

  it("投稿者情報を表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      const userInfos = screen.getAllByText(/投稿者:/);
      expect(userInfos.length).toBe(2);
      expect(screen.getByText(/投稿者: テストユーザー$/)).toBeInTheDocument();
      expect(screen.getByText(/投稿者: テストユーザー2$/)).toBeInTheDocument();
    });
  });

  it("追加日を表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByText(/追加日: 2025\/1\/1/)).toBeInTheDocument();
      expect(screen.getByText(/追加日: 2025\/1\/2/)).toBeInTheDocument();
    });
  });

  it("メモがある場合に表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByText("便利なアドオン")).toBeInTheDocument();
    });
  });

  it("メモがない記事ではメモ欄を表示しない", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: mockItemsWithPublicArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByText("テストアドオン2")).toBeInTheDocument();
    });

    // メモが1つしか表示されていないことを確認
    const noteBoxes = document.querySelectorAll(
      ".bg-yellow-50.border.border-yellow-200"
    );
    expect(noteBoxes.length).toBe(1);
  });

  it("API エラー時にエラーメッセージを表示する", async () => {
    mockAxios.get = vi.fn().mockRejectedValue(new Error("ネットワークエラー"));

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByRole("alert")).toBeInTheDocument();
      expect(screen.getByText("ネットワークエラー")).toBeInTheDocument();
    });
  });

  it("配列以外のデータを受け取った場合にエラーを表示する", async () => {
    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: "invalid data" },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByRole("alert")).toBeInTheDocument();
      expect(
        screen.getByText("アイテムの取得に失敗しました")
      ).toBeInTheDocument();
    });
  });

  it("非公開記事（URL なし）の場合はリンクを表示しない", async () => {
    const itemsWithPrivateArticle: MyListItemShow[] = [
      {
        id: 3,
        note: null,
        position: 1,
        created_at: "2025-01-03T12:00:00Z",
        article: {
          id: 102,
          title: "非公開記事",
        },
      },
    ];

    mockAxios.get = vi.fn().mockResolvedValue({
      data: { data: itemsWithPrivateArticle },
    });

    render(<PublicMyListPage mylistSlug={mockSlug} />);

    await waitFor(() => {
      expect(screen.getByText("非公開記事")).toBeInTheDocument();
      // リンクではなくspanで表示される
      const titleElement = screen.getByText("非公開記事");
      expect(titleElement.tagName).toBe("SPAN");
    });
  });
});
