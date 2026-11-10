import { useState } from "react";
import { Pagination } from "@/components/layout/Pagination";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/components/layout/DataTable";
import { compareArticleValues } from "../articles/utils/articleUtil";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";
import { analyticsFilter } from "./analyticsUtil";
import { format } from "date-fns";
import { FormCaption } from "@/components/ui/FormCaption";
import Input from "@/components/ui/Input";
import Button from "@/components/ui/Button";

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
  {
    name: "累計PV数",
    key: "total_view_count",
    width: "w-1/10",
    sortable: true,
  },
  {
    name: "累計CV数",
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
  const activePage = Math.min(current, Math.max(1, totalPages));

  const sorted = [...filtered].sort((a, b) => {
    const result = compareArticleValues(a[sort.column], b[sort.column]);
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof Analytics.Article) => {
    setCurrent(1);
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
    } else if (selected.length < 10) {
      set({ selected: [...selected, articleId] });
    }
  };

  return (
    <>
      <FormCaption>表示記事</FormCaption>
      <div className="flex flex-wrap items-center gap-3">
        <p role="status" className="text-sm">
          選択済み {selected.length} / 10件
        </p>
        <Button
          variant="sub"
          size="sm"
          onClick={() => set({ selected: [] })}
          disabled={selected.length === 0}
        >
          全解除
        </Button>
      </div>
      <p className="text-sm text-c-sub">
        記事を最大10件まで選択できます。一覧の PV・CV は全期間の累計です。
      </p>
      <div className="v2-table-container">
        <div>
          <Input
            className="w-full"
            type="search"
            value={criteria}
            onChange={(e) => {
              setCriteria(e.target.value);
              setCurrent(1);
            }}
            placeholder="検索"
          />
        </div>
        <div className="grow"></div>
        <Pagination
          total={totalPages}
          current={activePage}
          onChange={setCurrent}
        />
      </div>
      <div className="v2-table-wrapper">
        <DataTable
          headers={headers}
          data={sorted}
          sort={sort}
          limit={limit}
          current={activePage}
          onSort={onSort}
          renderRow={(article) => (
            <tr
              key={article.id}
              className={twMerge(
                selected.includes(article.id)
                  ? "cursor-pointer v2-hover-bg-primary"
                  : "cursor-pointer v2-hover-bg-sub"
              )}
              onClick={() => onClick(article.id)}
            >
              <td>
                <div className="flex items-center gap-2">
                  <input
                    type="checkbox"
                    className="v2-checkbox"
                    aria-label={`${article.title}を選択`}
                    checked={selected.includes(article.id)}
                    disabled={
                      selected.length >= 10 && !selected.includes(article.id)
                    }
                    onClick={(event) => event.stopPropagation()}
                    onChange={() => onClick(article.id)}
                  />
                  {article.title}
                </div>
              </td>
              <td>{format(new Date(article.published_at), "yyyy/MM/dd")}</td>
              <td>{format(new Date(article.modified_at), "yyyy/MM/dd")}</td>
              <td>{article.total_view_count?.count ?? 0}</td>
              <td>{article.total_conversion_count?.count ?? 0}</td>
            </tr>
          )}
        />
      </div>
    </>
  );
};
