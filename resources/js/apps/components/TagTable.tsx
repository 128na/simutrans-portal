import { useState } from "react";
import { Pagination } from "./Pagination";

type Props = {
  tags: Tag[];
  limit: number;
  onClick?: (tag: Tag) => void;
};

export const TagTable = ({ tags, limit, onClick }: Props) => {
  const [criteria, setCriteria] = useState("");
  const [current, setCurrent] = useState(1);
  const filteredTags = tags.filter(
    (t) =>
      t.name.toLowerCase().includes(criteria.toLowerCase()) ||
      t.description?.toLowerCase().includes(criteria.toLowerCase()) ||
      t.created_by?.name.toLowerCase().includes(criteria.toLowerCase()) ||
      t.last_modified_by?.name.toLowerCase().includes(criteria.toLowerCase()),
  );
  const totalPages = Math.ceil(filteredTags.length / limit);

  return (
    <div className="relative overflow-x-auto">
      <div className="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between py-4 px-1">
        <Pagination
          total={totalPages}
          current={current}
          onChange={setCurrent}
        />
        <div>
          <input
            type="text"
            value={criteria}
            onChange={(e) => setCriteria(e.target.value)}
            placeholder="検索"
            className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>
      <table className="table-fixed w-full text-sm text-left ">
        <thead>
          <tr className="text-xs text-gray-700 bg-gray-50">
            <th className="w-2/12 px-6 py-3">タグ名</th>
            <th className="w-3/12 px-6 py-3">説明</th>
            <th className="w-1/12 px-6 py-3">記事数</th>
            <th className="w-2/12 px-6 py-3">作成者</th>
            <th className="w-2/12 px-6 py-3 cursor-pointer">最終更新者</th>
          </tr>
        </thead>
        <tbody>
          {filteredTags
            .slice((current - 1) * limit, current * limit)
            .map((tag) => (
              <tr
                key={tag.id}
                className={`bg-white border-b border-gray-200 ${tag.editable ? "cursor-pointer hover:bg-gray-100" : ""}`}
                onClick={() => onClick && onClick(tag)}
              >
                <td className="px-6 py-4 font-medium">{tag.name}</td>
                <td className="px-6 py-4">{tag.description}</td>
                <td className="px-6 py-4">{tag.articles_count}</td>
                <td className="px-6 py-4">
                  {tag.created_by ? tag.created_by.name : "-"}
                </td>
                <td className="px-6 py-4">
                  {tag.last_modified_by ? tag.last_modified_by.name : "-"}
                </td>
              </tr>
            ))}
        </tbody>
      </table>
    </div>
  );
};
