import { useState } from "react";
import { Pagination } from "@/components/layout/Pagination";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/components/layout/DataTable";
import {
  articleFilter,
  compareArticleValues,
  PostTypeText,
  StatusClass,
  StatusText,
} from "./utils/articleUtil";
import { format } from "date-fns";
import V2Button from "@/components/ui/v2/V2Button";
import V2Select from "@/components/ui/v2/V2Select";
import V2Input from "@/components/ui/v2/V2Input";

type Props = {
  articles: Article.MypageShow[];
  limit: number;
  onClick?: (article: Article.MypageShow) => void;
};

type Sort = {
  column: keyof Article.MypageShow;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Article.MypageShow>[] = [
  { name: "タイトル", key: "title", width: "w-4/12", sortable: true },
  { name: "ステータス", key: "status", width: "w-2/12", sortable: true },
  { name: "投稿形式", key: "post_type", width: "w-2/12", sortable: true },
  { name: "公開日時", key: "published_at", width: "w-2/12", sortable: true },
  { name: "更新日時", key: "modified_at", width: "w-2/12", sortable: true },
  { name: "PV数", key: "total_view_count", width: "w-1/12", sortable: true },
  {
    name: "CV数",
    key: "total_conversion_count",
    width: "w-1/12",
    sortable: true,
  },
];

export const ArticleTable = ({ articles, limit, onClick }: Props) => {
  const [criteria, setCriteria] = useState("");
  const [statusFilter, setStatusFilter] = useState<ArticleStatus | "">("");
  const [postTypeFilter, setPostTypeFilter] = useState<ArticlePostType | "">(
    ""
  );
  const [sort, setSort] = useState<Sort>({
    column: "modified_at",
    order: "desc",
  });
  const [current, setCurrent] = useState(1);

  const filtereArticles = articleFilter(articles, criteria)
    .filter((article) => !statusFilter || article.status === statusFilter)
    .filter(
      (article) => !postTypeFilter || article.post_type === postTypeFilter
    );

  const totalPages = Math.ceil(filtereArticles.length / limit);

  const sortedArticles = [...filtereArticles].sort((a, b) => {
    const result = compareArticleValues(
      a[sort.column] ?? 0,
      b[sort.column] ?? 0
    );
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof Article.MypageShow) => {
    setSort((prev) =>
      prev.column === column
        ? { column, order: prev.order === "asc" ? "desc" : "asc" }
        : { column, order: "asc" }
    );
  };

  const createUrl = `${import.meta.env.VITE_APP_URL}/mypage/articles/create`;
  return (
    <>
      <div className="v2-table-container">
        <div>
          <V2Button
            size="lg"
            onClick={() => (window.location.href = createUrl)}
          >
            作成
          </V2Button>
        </div>
        <div>
          <V2Select
            value={statusFilter}
            onChange={(e) =>
              setStatusFilter(e.target.value as ArticleStatus | "")
            }
            options={{
              "": "すべてのステータス",
              publish: StatusText["publish"],
              reservation: StatusText["reservation"],
              draft: StatusText["draft"],
              private: StatusText["private"],
              trash: StatusText["trash"],
            }}
          />
        </div>
        <div>
          <V2Select
            value={postTypeFilter}
            onChange={(e) =>
              setPostTypeFilter(e.target.value as ArticlePostType | "")
            }
            options={{
              "": "すべての投稿形式",
              "addon-post": PostTypeText["addon-post"],
              "addon-introduction": PostTypeText["addon-introduction"],
              page: PostTypeText["page"],
              markdown: PostTypeText["markdown"],
            }}
          />
        </div>
        <div>
          <V2Input
            type="search"
            value={criteria}
            onChange={(e) => setCriteria(e.target.value)}
            placeholder="検索"
          />
        </div>
        <div className="grow"></div>
        <Pagination
          total={totalPages}
          current={current}
          onChange={setCurrent}
        />
      </div>
      <div className="relative overflow-x-auto">
        <DataTable
          headers={headers}
          data={sortedArticles}
          sort={sort}
          limit={limit}
          current={current}
          onSort={onSort}
          renderRow={(article) => (
            <tr
              key={article.id}
              className={twMerge("cursor-pointer", StatusClass[article.status])}
              onClick={() => onClick?.(article)}
            >
              <td>{article.title}</td>
              <td>{StatusText[article.status]}</td>
              <td>{PostTypeText[article.post_type]}</td>
              <td>
                {article.published_at
                  ? format(new Date(article.published_at), "yyyy/MM/dd H:mm")
                  : "-"}
                {article.status === "reservation" && "（予約）"}
              </td>
              <td>
                {format(new Date(article.modified_at), "yyyy/MM/dd H:mm")}
              </td>
              <td>{article.total_view_count?.count ?? 0}</td>
              <td>{article.total_conversion_count?.count ?? 0}</td>
            </tr>
          )}
        />
      </div>
    </>
  );
};
