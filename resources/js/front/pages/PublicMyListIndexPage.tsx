import axios from "axios";
import { useEffect, useMemo, useState } from "react";
import { createRoot } from "react-dom/client";
import { AppWrapper } from "@/components/AppWrapper";
import { Pagination } from "@/components/layout/Pagination";
import type { MyListShow } from "@/types/models";
import type { PaginationInfo, PaginationLinks } from "@/types/utils";

const DEFAULT_PER_PAGE = 20;
const DEFAULT_SORT = "updated_at:desc";

type PublicMyListResponse = {
  data: MyListShow[];
  links: PaginationLinks;
  meta: PaginationInfo;
};

const PublicMyListIndexPage = () => {
  const [lists, setLists] = useState<MyListShow[]>([]);
  const [meta, setMeta] = useState<PaginationInfo | null>(null);
  const [links, setLinks] = useState<PaginationLinks | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const initialPage = useMemo(() => {
    const params = new URLSearchParams(window.location.search);
    const pageParam = Number(params.get("page"));
    return Number.isFinite(pageParam) && pageParam > 0 ? pageParam : 1;
  }, []);

  const [currentPage, setCurrentPage] = useState(initialPage);

  const updateUrl = (page: number) => {
    const params = new URLSearchParams(window.location.search);
    if (page <= 1) {
      params.delete("page");
    } else {
      params.set("page", String(page));
    }
    const query = params.toString();
    const nextUrl = query
      ? `${window.location.pathname}?${query}`
      : window.location.pathname;
    window.history.replaceState({}, "", nextUrl);
  };

  const fetchLists = async (page: number) => {
    try {
      setIsLoading(true);
      setError(null);

      const { data } = await axios.get<PublicMyListResponse>(
        "/api/v1/mylist/public",
        {
          params: {
            page,
            per_page: DEFAULT_PER_PAGE,
            sort: DEFAULT_SORT,
          },
        }
      );

      if (!Array.isArray(data.data)) {
        throw new Error("リストの取得に失敗しました");
      }

      setLists(data.data);
      setMeta(data.meta ?? null);
      setLinks(data.links ?? null);
    } catch (err) {
      setError(err instanceof Error ? err.message : "エラーが発生しました");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchLists(currentPage);
    updateUrl(currentPage);
  }, [currentPage]);

  const totalPages = meta?.last_page ?? 1;

  return (
    <div className="space-y-6">
      {error && (
        <div className="v2-card v2-card-danger" role="alert">
          <p className="v2-text-body">{error}</p>
        </div>
      )}

      {isLoading ? (
        <div className="v2-card v2-card-main">
          <div className="v2-text-center py-12">
            <p className="v2-text-body text-gray-500">読み込み中...</p>
          </div>
        </div>
      ) : lists.length === 0 ? (
        <div className="v2-card v2-card-main">
          <div className="v2-text-center py-12">
            <p className="v2-text-body text-gray-500">
              公開マイリストがありません。
            </p>
          </div>
        </div>
      ) : (
        <div className="space-y-4">
          {lists.map((list) => (
            <div key={list.id} className="v2-card v2-card-main">
              <div className="flex items-start justify-between gap-4">
                <div className="min-w-0">
                  <div className="text-sm v2-text-sub mb-2">
                    アイテム数: {list.items_count ?? 0}件
                  </div>
                  <h3 className="v2-text-h3 mb-2">
                    {list.slug ? (
                      <a href={`/mylist/${list.slug}`} className="v2-link">
                        {list.title}
                      </a>
                    ) : (
                      <span>{list.title}</span>
                    )}
                  </h3>
                  {list.note && (
                    <p className="text-sm v2-text-sub whitespace-pre-wrap line-clamp-3">
                      {list.note}
                    </p>
                  )}
                </div>
                <div className="text-sm v2-text-sub whitespace-nowrap">
                  更新日: {list.updated_at}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {totalPages > 1 && (
        <div className="v2-page-pagination-area">
          <Pagination
            total={totalPages}
            current={currentPage}
            onChange={setCurrentPage}
          />
        </div>
      )}

      {links?.next && !isLoading && (
        <div className="text-sm v2-text-sub text-right">
          次のページがあります
        </div>
      )}
    </div>
  );
};

const container = document.getElementById("app-public-mylist-index");
if (container) {
  createRoot(container).render(
    <AppWrapper boundaryName="PublicMyListIndexPage">
      <PublicMyListIndexPage />
    </AppWrapper>
  );
}
