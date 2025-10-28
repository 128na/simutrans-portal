import { useState } from "react";
import { Pagination } from "./Pagination";
import { compareTagValues, tagFilter } from "../libs/tagTool";
import Button from "../elements/Button";
import Input from "../elements/Input";
import { twMerge } from "tailwind-merge";

type Props = {
  tags: Tag[];
  limit: number;
  onClick?: (tag: Tag | NewTag) => void;
};

type Sort = {
  column: keyof Tag;
  order: "asc" | "desc";
};

const headers: { name: string; key: keyof Tag; width: string }[] = [
  { name: "タグ名", key: "name", width: "w-2/12" },
  { name: "説明", key: "description", width: "w-3/12" },
  { name: "記事数", key: "articles_count", width: "w-1/12" },
  { name: "作成者", key: "created_by", width: "w-2/12" },
  { name: "最終更新者", key: "last_modified_by", width: "w-2/12" },
  { name: "最終更新日", key: "last_modified_at", width: "w-2/12" },
];

export const TagTable = ({ tags, limit, onClick }: Props) => {
  const [criteria, setCriteria] = useState("");
  const [sort, setSort] = useState<Sort>({
    column: "last_modified_at",
    order: "desc",
  });
  const [current, setCurrent] = useState(1);

  const filteredTags = tagFilter(tags, criteria);

  const totalPages = Math.ceil(filteredTags.length / limit);

  const sortedTags = [...filteredTags].sort((a, b) => {
    const result = compareTagValues(a[sort.column], b[sort.column]);
    return sort.order === "asc" ? result : -result;
  });

  const handleSort = (column: keyof Tag) => {
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
      <table className="table-fixed w-full text-sm text-left">
        <thead>
          <tr className="text-xs text-gray-700 bg-gray-50">
            {headers.map((header) => (
              <th
                key={header.key}
                onClick={() => handleSort(header.key)}
                className={twMerge("px-6 py-3 cursor-pointer", header.width)}
              >
                {header.name}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {sortedTags
            .slice((current - 1) * limit, current * limit)
            .map((tag) => (
              <tr
                key={tag.id}
                className={twMerge(
                  "bg-white border-b border-gray-200",
                  tag.editable ? "cursor-pointer hover:bg-gray-100" : "",
                )}
                onClick={() => onClick?.(tag)}
              >
                <td className="px-6 py-4 font-medium">{tag.name}</td>
                <td className="px-6 py-4 whitespace-pre-wrap">
                  {tag.description ?? "-"}
                </td>
                <td className="px-6 py-4">{tag.articles_count}</td>
                <td className="px-6 py-4">{tag.created_by?.name ?? "-"}</td>
                <td className="px-6 py-4">
                  {tag.last_modified_by?.name ?? "-"}
                </td>
                <td className="px-6 py-4">
                  {tag.last_modified_at?.slice(0, 10)}
                </td>
              </tr>
            ))}
        </tbody>
      </table>
    </div>
  );
};
