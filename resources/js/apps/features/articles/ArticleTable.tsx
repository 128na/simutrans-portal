import { useState } from "react";
import { Pagination } from "@/apps/components/layout/Pagination";
import Input from "@/apps/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/apps/components/layout/DataTable";
import {
  articleFilter,
  compareArticleValues,
  PostTypeText,
  StatusClass,
  StatusText,
} from "./articleUtil";
import { format } from "date-fns";

type Props = {
  user: User;
  articles: ListingArticle[];
  limit: number;
  onClick?: (article: ListingArticle) => void;
};

type Sort = {
  column: keyof ListingArticle;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof ListingArticle>[] = [
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
  const [sort, setSort] = useState<Sort>({
    column: "modified_at",
    order: "desc",
  });
  const [current, setCurrent] = useState(1);

  const filtereArticles = articleFilter(articles, criteria);

  const totalPages = Math.ceil(filtereArticles.length / limit);

  const sortedArticles = [...filtereArticles].sort((a, b) => {
    const result = compareArticleValues(
      a[sort.column] ?? 0,
      b[sort.column] ?? 0,
    );
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof ListingArticle) => {
    setSort((prev) =>
      prev.column === column
        ? { column, order: prev.order === "asc" ? "desc" : "asc" }
        : { column, order: "asc" },
    );
  };

  return (
    <div className="relative overflow-x-auto">
      <div className="gap-4 flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between py-4">
        <div>
          <Input
            type="text"
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
            className={twMerge(
              "bg-white border-b border-gray-200 cursor-pointer",
              StatusClass[article.status],
            )}
            onClick={() => onClick?.(article)}
          >
            <td className="px-6 py-4 font-medium">{article.title}</td>
            <td className="px-6 py-4">{StatusText[article.status]}</td>
            <td className="px-6 py-4">{PostTypeText[article.post_type]}</td>
            <td className="px-6 py-4">
              {article.published_at
                ? format(new Date(article.published_at), "yyyy/MM/dd H:mm")
                : "-"}
              {article.status === "reservation" && "（予約）"}
            </td>
            <td className="px-6 py-4">
              {format(new Date(article.modified_at), "yyyy/MM/dd H:mm")}
            </td>
            <td className="px-6 py-4">
              {article.total_view_count?.count ?? 0}
            </td>
            <td className="px-6 py-4">
              {article.total_conversion_count?.count ?? 0}
            </td>
          </tr>
        )}
      />
    </div>
  );
};
