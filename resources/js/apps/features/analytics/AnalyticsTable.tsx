import { useState } from "react";
import { Pagination } from "@/apps/components/layout/Pagination";
import Input from "@/apps/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/apps/components/layout/DataTable";
import { compareArticleValues } from "../articles/articleUtil";
import { useAnalyticsStore } from "@/apps/state/useAnalyticsStore";
import { analyticsFilter } from "./analyticsUtil";

type Props = {
  articles: Analytics.Article[];
  limit: number;
};

type Sort = {
  column: keyof Analytics.Article;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Analytics.Article>[] = [
  { name: "タイトル", key: "title", width: "w-2/4", sortable: true },
  { name: "投稿日", key: "published_at", width: "w-1/4", sortable: true },
  { name: "更新日", key: "modified_at", width: "w-1/4", sortable: true },
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
        : { column, order: "asc" },
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
                  : "cursor-pointer hover:bg-gray-100",
              )}
              onClick={() => onClick(article.id)}
            >
              <td className="px-6 py-4 font-medium">{article.title}</td>
              <td className="px-6 py-4">
                {article.published_at?.slice(0, 10)}
              </td>
              <td className="px-6 py-4">{article.modified_at?.slice(0, 10)}</td>
            </tr>
          )}
        />
      </div>
    </>
  );
};
