import { useState } from "react";
import { Pagination } from "@/components/layout/Pagination";
import { compareTagValues, tagFilter } from "./tagUtil";
import Button from "@/components/ui/Button";
import Input from "@/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/components/layout/DataTable";
import TextSub from "@/components/ui/TextSub";

type Props = {
  tags: Tag.MypageEdit[];
  limit: number;
  onClick?: (tag: Tag.MypageEdit | Tag.New) => void;
};

type Sort = {
  column: keyof Tag.MypageEdit;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Tag.MypageEdit>[] = [
  { name: "タグ名", key: "name", width: "w-2/12", sortable: true },
  { name: "説明", key: "description", width: "w-3/12", sortable: true },
  { name: "記事数", key: "articles_count", width: "w-1/12", sortable: true },
  { name: "作成者", key: "created_by", width: "w-2/12", sortable: true },
  { name: "更新者", key: "last_modified_by", width: "w-2/12", sortable: true },
  { name: "更新日", key: "last_modified_at", width: "w-2/12", sortable: true },
];

export const TagTable = ({ tags, limit, onClick }: Props) => {
  const [criteria, setCriteria] = useState("");
  const [sort, setSort] = useState<Sort>({
    column: "last_modified_at",
    order: "desc",
  });
  const [current, setCurrent] = useState(1);

  const filtered = tagFilter(tags, criteria);

  const totalPages = Math.ceil(filtered.length / limit);

  const sorted = filtered.sort((a, b) => {
    const result = compareTagValues(a[sort.column], b[sort.column]);
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof Tag.MypageEdit) => {
    setSort((prev) =>
      prev.column === column
        ? { column, order: prev.order === "asc" ? "desc" : "asc" }
        : { column, order: "asc" }
    );
  };

  return (
    <>
      <div className="gap-4 flex flex-col sm:flex-row pb-4">
        <div>
          <Button
            onClick={() =>
              onClick?.({ id: null, name: criteria, description: null })
            }
          >
            作成
          </Button>
        </div>
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
      <TextSub>紐づく記事が無いタグは数日後に自動的に削除されます。</TextSub>
      <div className="relative overflow-x-auto">
        <DataTable
          headers={headers}
          data={sorted}
          sort={sort}
          limit={limit}
          current={current}
          onSort={onSort}
          renderRow={(tag) => (
            <tr
              key={tag.id}
              className={twMerge(
                "bg-white border-b border-c-sub/10",
                tag.editable && "cursor-pointer hover:bg-c-sub"
              )}
              onClick={() => onClick?.(tag)}
            >
              <td className="px-6 py-4 font-medium">{tag.name}</td>
              <td className="px-6 py-4 whitespace-pre-wrap">
                {tag.description ?? "-"}
              </td>
              <td className="px-6 py-4">{tag.articles_count}</td>
              <td className="px-6 py-4">{tag.created_by?.name ?? "-"}</td>
              <td className="px-6 py-4">{tag.last_modified_by?.name ?? "-"}</td>
              <td className="px-6 py-4">
                {tag.last_modified_at?.slice(0, 10)}
              </td>
            </tr>
          )}
        />
      </div>
    </>
  );
};
