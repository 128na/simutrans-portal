import { useState } from "react";
import { Pagination } from "@/components/layout/Pagination";
import Input from "@/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/components/layout/DataTable";
import { compareArticleValues } from "../articles/utils/articleUtil";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";
import { analyticsFilter } from "./analyticsUtil";
import { format } from "date-fns";
import { FormCaption } from "@/components/ui/FormCaption";

type Props = {
  articles: Analytics.Article[];
  limit: number;
};

type Sort = {
  column: keyof Analytics.Article;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Analytics.Article>[] = [
  { name: "タイトル", key: "title", width: "w-4/10", sortable: true },
  { name: "投稿日", key: "published_at", width: "w-2/10", sortable: true },
  { name: "更新日", key: "modified_at", width: "w-2/10", sortable: true },
  { name: "PV数", key: "total_view_count", width: "w-1/10", sortable: true },
  {
    name: "CV数",
    key: "total_conversion_count",
    width: "w-1/10",
    sortable: true,
  },
];

export const AnalyticsTable = ({ articles, limit }: Props) => {
  const { selected, set } = useAnalyticsStore();

  const [criteria, setCriteria] = useState("");
  const [sort, setSort] = useState<Sort>({
    column: "modified_at",
    order: "desc",
  });
  const [current, setCurrent] = useState(1);

  const filtered = analyticsFilter(articles, criteria, selected);

  const totalPages = Math.ceil(filtered.length / limit);

  const sorted = filtered.sort((a, b) => {
    const result = compareArticleValues(a[sort.column], b[sort.column]);
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof Analytics.Article) => {
    setSort((prev) =>
      prev.column === column
        ? { column, order: prev.order === "asc" ? "desc" : "asc" }
        : { column, order: "asc" }
    );
  };

  const onClick = (articleId: number) => {
    const idx = selected.indexOf(articleId);
    if (idx >= 0) {
      set({ selected: selected.filter((id) => id !== articleId) });
    } else {
      if (selected.length >= 10) {
        alert("選択できるのは10記事までです。");
        return;
      }
      set({ selected: [...selected, articleId] });
    }
  };

  return (
    <>
      <FormCaption>表示記事</FormCaption>
      <div className="gap-4 flex flex-col sm:flex-row pb-4">
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
      <div className="relative overflow-x-auto">
        <DataTable
          headers={headers}
          data={sorted}
          sort={sort}
          limit={limit}
          current={current}
          onSort={onSort}
          renderRow={(article) => (
            <tr
              key={article.id}
              className={twMerge(
                "bg-white border-b border-gray-200",
                selected.includes(article.id)
                  ? "cursor-pointer bg-brand/20 hover:bg-brand/30"
                  : "cursor-pointer hover:bg-gray-100"
              )}
              onClick={() => onClick(article.id)}
            >
              <td className="px-6 py-4 font-medium">{article.title}</td>
              <td className="px-6 py-4">
                {format(new Date(article.published_at), "yyyy/MM/dd")}
              </td>
              <td className="px-6 py-4">
                {format(new Date(article.modified_at), "yyyy/MM/dd")}
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
    </>
  );
};
