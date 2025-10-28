import { useState } from "react";
import { Pagination } from "./Pagination";
import { compareTagValues, tagFilter } from "../libs/tagTool";

type Props = {
  tags: Tag[];
  limit: number;
  onClick?: (tag: Tag | NewTag) => void;
};

type Sort = {
  column: keyof Tag;
  order: "asc" | "desc";
};

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
          <button
            onClick={() =>
              onClick?.({ id: null, name: criteria, description: null })
            }
            className="px-4 py-2 bg-brand text-white rounded-lg cursor-pointer"
          >
            作成
          </button>
        </div>
        <div>
          <input
            type="text"
            value={criteria}
            onChange={(e) => setCriteria(e.target.value)}
            placeholder="検索"
            className="w-full border border-gray-300 rounded-lg px-4 py-2"
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
            <th
              onClick={() => handleSort("name")}
              className="px-6 py-3 cursor-pointer w-2/12"
            >
              タグ名
            </th>
            <th
              onClick={() => handleSort("description")}
              className="px-6 py-3 cursor-pointer w-3/12"
            >
              説明
            </th>
            <th
              onClick={() => handleSort("articles_count")}
              className="px-6 py-3 cursor-pointer w-1/12"
            >
              記事数
            </th>
            <th
              onClick={() => handleSort("created_by")}
              className="px-6 py-3 cursor-pointer w-2/12"
            >
              作成者
            </th>
            <th
              onClick={() => handleSort("last_modified_by")}
              className="px-6 py-3 cursor-pointer w-2/12"
            >
              最終更新者
            </th>
            <th
              onClick={() => handleSort("last_modified_at")}
              className="px-6 py-3 cursor-pointer w-2/12"
            >
              最終更新日
            </th>
          </tr>
        </thead>
        <tbody>
          {sortedTags
            .slice((current - 1) * limit, current * limit)
            .map((tag) => (
              <tr
                key={tag.id}
                className={`bg-white border-b border-gray-200 ${
                  tag.editable ? "cursor-pointer hover:bg-gray-100" : ""
                }`}
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
